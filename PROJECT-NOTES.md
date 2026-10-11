# Kresge Carpentry — Project Notes

Last updated: 2026-10-11. Personal notes for picking this back up. The technical how-to lives in [README.md](README.md).

## Where things stand

- **Static site** (`index.html`, `assets/`): sage-green rebrand + design-critique fixes (mobile layout, honest project photos/copy, heading scale, contact block, accessibility). Done.
- **WordPress version** (`wp-theme/kresge/` + `wp-plugin/kresge-core/`): done and tested locally. Owner can edit homepage text/photos, add projects, change phone/email in one place. Passes Theme Check.
- **Not done yet:** pitched to the client, real business details, hosting, launch.
- Branch: `brand-identity-refresh` (not pushed; not merged to `main`).

## Documents (Claude Docs)

| Doc | For | Link |
| --- | --- | --- |
| Kresge Carpentry Website Guide | The owner: step-by-step how-to | https://claude.ai/code/artifact/03c10ba2-ad0d-4bb4-8e85-28c88f19a796 |
| Website Services & Trade Agreement | Contract, itemized $4,500 trade | https://claude.ai/code/artifact/e9c0be17-247c-4827-85c4-9a58da4c978c |

Before sending: fill contract blanks (addresses, date, carpentry job, completion date, state), check the $85/hr and 90-day numbers, swap "yourdomain.com" and add your contact info in the guide. Have a lawyer review the contract once.

In-repo specs: `docs/superpowers/specs/` (design) and `docs/superpowers/plans/` (build plan). Old static-site quote and contract: `docs/archive/`.

## See it running

```bash
npm run wp
```

Then open http://127.0.0.1:9400 (visit the homepage first; it logs you in), dashboard at `/wp-admin/`. Changes reset every restart. Static site: `python3 -m http.server 8765` → http://localhost:8765. First time on a new machine: `npm install --ignore-scripts && npm rebuild node`.

## Value and pricing

Fair market value about **$3,500–6,000**; using **$4,500** for the trade.

| Piece | Range |
| --- | --- |
| Design | $1,200–2,500 |
| Custom WordPress theme | $1,500–3,000 |
| Owner-editing setup | $500–1,000 |
| Form, SEO, launch | $300–800 |

Trade tips: dollar value on both sides, in writing; hosting/domain/maintenance not included; aftercare plan $50–150/mo or hourly/trade; barter is taxable income for both sides (ask a tax person).

## The pitch

- Lead by showing the site on your phone next to their Facebook page.
- Google "deck builder [their town]" with them; competitors with sites make the point.
- Why a site: people Google before calling; Facebook isn't theirs; credibility for big jobs; projects don't sink in a feed; Google Business Profile + site = Maps listings.
- Easy out: "No pressure — if it's not useful, we keep things as they are." Contract comes out only once they're interested.
- Objections: "Facebook works" → it's for people who already know you. "I won't update it" → 5 minutes, here's the guide. "Cost?" → trade, plus ~$5–30/mo hosting.

## Launch checklist

- [ ] Signed contract (or written approval) before starting
- [ ] Domain + hosting in the client's name and card; you added as a user
- [ ] Logins collected: registrar, host, form email inbox
- [ ] Photo rights confirmed; real testimonial with permission
- [ ] Real phone/email/area in Business details (555 number is a placeholder)
- [ ] Contractor license number in footer if their state requires it
- [ ] Privacy policy page (Settings → Privacy)
- [ ] Plugins: Fluent Forms, SEO (Yoast/Rank Math), WP Mail SMTP, backups (UpdraftPlus) unless host covers it; security/caching/image compression if host doesn't
- [ ] Spam protection on the form (Turnstile/reCAPTCHA setting)
- [ ] Test the contact form on the live site
- [ ] Site icon + social share image
- [ ] Google Business Profile linked; name/address/phone match exactly
- [ ] Analytics (Site Kit) + uptime monitor (UptimeRobot)
- [ ] Full backup at handoff, keep a copy
- [ ] 30-minute walkthrough + hand over guide
- [ ] Calendar reminders: domain renewal, end of 30-day fix window

## Known small issues (deferred)

Phone extensions get added to the tel: link · "&" shows as `&amp;` in Google business data · invalid email cleared without a warning · editor labels every linked field "Business details" · hero image stored as a full URL after first save · blank right half of contact card if Fluent Forms is off · `/#…` links break on subfolder installs · mobile-menu links 32px tall · 900px menu switch relies on WordPress class names · WordPress version not pinned in `wp-dev/blueprint.json` · no one-click full homepage pattern for deploys.

## Deploy (when it's a go)

Zip `wp-theme/kresge` and `wp-plugin/kresge-core` separately → upload plugin, then theme on the client's host → install Fluent Forms and the plugins above → build the Home page from the Kresge section patterns → Settings → Reading → set Home as homepage → Business details → add projects. Details in [README.md](README.md).
