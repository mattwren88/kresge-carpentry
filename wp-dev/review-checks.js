// Regression checks for the final-review findings. Run with Playwright MCP's
// browser_run_code (filename) against `npm run wp`; restores site state after.
async (page) => {
  const BASE = 'http://127.0.0.1:9400';
  const results = {};
  const anonContext = await page.context().browser().newContext();
  // Playground's --login signs in every new browser unless this cookie is set.
  await anonContext.addCookies([{ name: 'playground_auto_login_already_happened', value: '1', url: BASE }]);
  const anon = await anonContext.newPage();
  const api = async (path, method = 'GET', data) => {
    const r = await page.evaluate(
      ([path, method, data]) => wp.apiFetch({ path, method, data }).then(v => ({ v }), e => ({ e: JSON.stringify(e) })),
      [path, method, data]
    );
    if (r.e) throw new Error(`${method} ${path}: ${r.e}`);
    return r.v;
  };
  await page.goto(`${BASE}/wp-admin/`);

  // Owner can add FAQ questions on the Home page.
  const home = await api('/wp/v2/pages?slug=home&_fields=id');
  await page.goto(`${BASE}/wp-admin/post.php?post=${home[0].id}&action=edit`);
  await page.waitForFunction(() => window.wp?.data?.select('core/block-editor').getBlocks().length > 0, null, { timeout: 60000 });
  await page.waitForTimeout(1500);
  results.faqAcceptsNewQuestions = await page.evaluate(() => {
    const be = wp.data.select('core/block-editor');
    const walk = (bs, out = []) => { bs.forEach(b => { out.push(b); walk(b.innerBlocks, out); }); return out; };
    const faq = walk(be.getBlocks()).find(b => (b.attributes.className || '').includes('kc-faq'));
    return be.canInsertBlockType('core/details', faq.clientId);
  });
  await page.goto(`${BASE}/wp-admin/`);

  // With one project, no orphaned "More recent builds" heading.
  const projects = await api('/wp/v2/project?per_page=20&_fields=id&orderby=date&order=desc');
  const older = projects.slice(1).map(p => p.id);
  for (const id of older) await api(`/wp/v2/project/${id}`, 'DELETE');
  await anon.goto(`${BASE}/`);
  results.oneProjectHidesEmptySubhead = await anon.evaluate(() => getComputedStyle(document.querySelector('.kc-subhead')).display === 'none');
  await api(`/wp/v2/project/${projects[0].id}`, 'DELETE');
  await anon.goto(`${BASE}/`);
  results.noProjectsHidesWorkSection = await anon.evaluate(() => getComputedStyle(document.getElementById('work')).display === 'none');
  for (const id of projects.map(p => p.id)) await api(`/wp/v2/project/${id}`, 'POST', { status: 'publish' });

  // Empty business details: visitors never see the admin hint.
  await page.goto(`${BASE}/wp-admin/options-general.php?page=kresge-business`);
  const saved = {};
  for (const k of ['phone', 'email', 'service_area']) { saved[k] = await page.inputValue(`#kresge-${k}`); await page.fill(`#kresge-${k}`, ''); }
  await page.click('#submit');
  await page.waitForLoadState('load');
  const anonHtml = await (await anon.request.get(`${BASE}/`)).text();
  const adminHtml = await (await page.request.get(`${BASE}/`)).text();
  results.visitorsDontSeeAdminHint = !anonHtml.includes('Add in Settings');
  results.adminsSeeHint = adminHtml.includes('Add in Settings');
  results.noEmptyHrefLinks = !/<a [^>]*href=""/.test(anonHtml);
  await page.goto(`${BASE}/wp-admin/options-general.php?page=kresge-business`);
  for (const k of Object.keys(saved)) await page.fill(`#kresge-${k}`, saved[k]);
  await page.click('#submit');
  await page.waitForLoadState('load');

  // Theme without the plugin: blog posts never appear as projects.
  await page.goto(`${BASE}/wp-admin/`);
  await api('/wp/v2/plugins/kresge-core/kresge-core', 'POST', { status: 'inactive' });
  const post = await api('/wp/v2/posts', 'POST', { title: 'Blog post not a project', status: 'publish' });
  const noPluginHtml = await (await anon.request.get(`${BASE}/`)).text();
  results.noPluginNoBlogPostsInWork = !noPluginHtml.includes('Blog post not a project');
  await api(`/wp/v2/posts/${post.id}?force=true`, 'DELETE');

  // Plugin activated after rewrite rules were built without it: /projects/ works.
  // Visiting Settings → Permalinks rebuilds the rules (here: without projects).
  await page.goto(`${BASE}/wp-admin/options-permalink.php`);
  await page.goto(`${BASE}/wp-admin/`);
  await api('/wp/v2/plugins/kresge-core/kresge-core', 'POST', { status: 'active' });
  results.projectsArchiveAfterLateActivation = (await anon.request.get(`${BASE}/projects/`)).status();

  await anonContext.close();
  return results;
}
