# CLAUDE.md — Curie Oncology Website (Static HTML)

## Project shape
Multi-page static HTML/CSS/JS site (not a single-file SPA, **not WordPress**). This is a change from the original plan — the project no longer runs on WordPress/PHP, and there is no CMS. Content (doctor profiles, health education articles, services, announcements) is hand-authored HTML in `src/pages/`, assembled with shared header/footer partials, and compiled to flat `.html` files at the repo root.

**Known consequence of dropping WordPress:** the client can no longer edit doctors/articles/announcements themselves post-launch without a developer making the change and redeploying. If the client still needs self-service editing, that's a decision to revisit later (e.g. a lightweight headless CMS, or re-introducing WordPress) — don't silently paper over this gap, flag it when it becomes relevant.

Read `SITEMAP.md` before building any page — it still maps the content plan for the site (it was written with a WordPress structure in mind; treat "Custom Post Type" / "taxonomy term" references there as *content groupings*, not literal implementation, since there's no CMS anymore).

Read `DESIGN-BRIEF.md` before styling anything — it's the teardown of the design reference plus the real Curie brand values (palette, type, imagery direction).

## Always Do First
- **Invoke the `frontend-design` skill** before writing any frontend code, every session, no exceptions.

## Design Reference
- Primary reference: `https://wp.verzinc.com/curie/` — match its layout, spacing, typography rhythm, and component patterns (header/nav, hero, value cards, team cards, forms, footer, floating WhatsApp widget) as closely as possible.
- Do not copy its color palette literally — use Curie's real brand palette instead (see `DESIGN-BRIEF.md`): sage/mint green as the primary accent (replaces the reference's teal), warm mustard/terracotta as a secondary accent, dark charcoal/navy text on white/off-white.
- Use the real logo and tagline from `brand_assets/logo/` — never a placeholder wordmark.
- Screenshot your output, compare against the reference site, fix mismatches, re-screenshot. Do at least 2 comparison rounds per page template. Stop only when no visible differences remain (beyond the intentional palette swap) or the user says so.
- Do not add sections, features, or content not called for in `SITEMAP.md`. Do not "improve" the reference layout — match it.

## Local Development
- This is a plain static site — no server-side runtime, no database, no local WordPress instance needed.
- Source of truth lives in `src/`: `src/pages/*.html` (per-page content), `src/partials/` (shared `header.html`, `footer.html`, `shell.html`), and `src/input.css` (Tailwind entry).
- `src/build.js` stitches partials + page content into the flat `index.html`, `about.html`, `our-doctors.html`, `our-services.html`, `health-education.html` files at the repo root.
- **Never hand-edit the root-level `.html` files directly** — they're generated output and will be overwritten on the next build. Edit `src/pages/` or `src/partials/`, then rebuild.
- Run `npm install` once, then `npm run build` (rebuilds pages + compiles Tailwind) or `npm run watch:css` while iterating on styles.
- Preview by serving the repo root (e.g. `npx http-server` or `npx live-server`) and opening the served URL — opening the `.html` files directly via `file://` also works since everything is relative, but a local server is more representative (and required for the View Transitions behavior described below).

## Screenshot Workflow
- Screenshot the locally-served site (Puppeteer script at `src/screenshot.js`, or the built-in browser preview) — see it rendered, not just the source.
- When comparing to the reference, be specific: "heading is 32px but reference shows ~24px", "card gap is 16px but should be 24px".
- Check: spacing/padding, font size/weight/line-height, colors (exact hex from the brand palette, not the reference's), alignment, border-radius, shadows, image sizing, and now animation timing/easing (see below).

## Site Architecture
- **Hand-coded static HTML** — no page builder, no framework, no bundler. Markup stays close to the reference design instead of fighting generated bloat.
- Styling: Tailwind CSS compiled via the **Tailwind CLI** (`npx tailwindcss -i src/input.css -o assets/css/style.css --watch`) — no PostCSS pipeline beyond what Tailwind ships with.
- Structure per page: hero → content sections → footer, each page built from `src/pages/<name>.html` wrapped by `src/partials/shell.html`.
- **Global elements on every page** (build once, into `src/partials/header.html` / `footer.html`, not per-page): sticky "Book Appointment" CTA, language switcher (EN/BM/中文), WhatsApp + call button widget.
- Doctors, services, and articles are currently individual hardcoded sections/cards in `src/pages/*.html`. Keep each doctor/service/article as a clearly separable markup block (consistent structure, easy to copy/duplicate) so a future migration to a CMS or data-driven template is straightforward, even though nothing is dynamically templated today.
- **Multilingual**: the header/footer already have an EN/BM/中文 language switcher in the markup; actual translated page variants are not yet built. When they are, keep the same static-file approach (e.g. `about.html`, `about-bm.html`, `about-zh.html`) rather than introducing a runtime i18n framework.
- **Forms**: the appointment/contact form on the homepage is static markup only (no backend). It needs either a form-endpoint service (e.g. Formspree, Netlify Forms) or a small serverless handler before it can actually receive submissions — flag this as a pre-launch blocker. It must keep the PDPA consent checkbox per the sitemap.

## Animation & Transition Effects
The site should feel considered and alive, not static — but restrained, since this is a healthcare site read by anxious patients. Motion should clarify hierarchy and reward attention, never distract or feel gimmicky.

- **Scroll reveals**: sections and cards fade/slide in on scroll (small translate-y + opacity, ~400–600ms, staggered by ~60–100ms across siblings). Implement with a small shared `IntersectionObserver` utility (vanilla JS, no library) included once via the footer partial so every page gets it for free. Add the observed elements a `data-reveal` attribute rather than inline JS per section.
- **Cross-page transitions**: since pages are separate static documents (full navigation, not a SPA), use the native **View Transitions API** (`@view-transition { navigation: auto }` in CSS, plus `::view-transition-old/-new` rules) to get smooth cross-document transitions in supporting browsers, with an automatic no-op fallback everywhere else. Don't reach for a JS router or a bundler-based framework just to get page transitions — that would contradict the "no bundler, no build framework" approach.
- **Micro-interactions**: every interactive element (buttons, cards, nav links, form fields) needs a deliberate hover/focus-visible/active transition — already partly in place via `.btn-primary`/`.btn-secondary` and card `hover:-translate-y-*` treatments. Extend this pattern to any new component rather than leaving it static.
- **Respect motion preferences**: wrap non-essential motion (scroll reveals, hover lifts, view transitions) in `@media (prefers-reduced-motion: no-preference)` or check `matchMedia('(prefers-reduced-motion: reduce)')` in JS before running the reveal observer. Reduced-motion users should still see all content, just without the animation.
- **Performance**: animate only `transform` and `opacity` (already a hard rule below) so everything stays GPU-composited — never animate `width`, `height`, `top`/`left`, or box-shadow spread directly.
- Keep easing consistent site-wide — define it once as a CSS custom property (spring-style, e.g. `cubic-bezier(0.34, 1.56, 0.64, 1)` for entrances, a gentler ease-out for hover states) rather than picking a new curve per component.

## Brand Assets
- Always check `brand_assets/` before designing. It contains the logo (PNG + JPG), four doctor headshots, and clinic/team photography.
- Use these real assets — do not use placeholder images or invent a wordmark where a real one exists.
- Colors: derive the palette from the logo's sage-green tagline and the clinic's warm mid-century interior tones (see `DESIGN-BRIEF.md`) — do not invent brand colors, and do not default to the reference site's teal.
- Content gap: only 4 of the doctors have headshots on file. Flag missing doctor photos/bios to the client rather than fabricating placeholder headshots for real staff.

## Anti-Generic Guardrails
- **Colors**: no default Tailwind palette (indigo-500, blue-600, etc.). Derive everything from the real sage-green/mustard brand palette.
- **Shadows**: no flat `shadow-md`. Use layered, color-tinted shadows with low opacity.
- **Typography**: pair the logo's serif with a clean sans for UI. Tight tracking (`-0.03em`) on large headings, generous line-height (`1.7`) on body text.
- **Animations**: only animate `transform` and `opacity`. Never `transition-all`. Spring-style easing. See the full Animation & Transition Effects section above.
- **Interactive states**: every clickable element needs hover, focus-visible, and active states — this is a healthcare site used by anxious patients; clarity over cleverness.
- **Images**: gradient overlay + color treatment on hero/banner imagery, consistent with the reference site's photo treatment.
- **Depth**: base → elevated → floating layering (cards, sticky CTA, modals), not everything on one plane.

## Version Control & Deployment
- Repo: `https://github.com/adibizuddinashari/curie-oncology.git`, `main` branch.
- The repo root holds the generated, servable site (`index.html`, `about.html`, etc. + `assets/`) alongside the `src/` source — this is intentional so the repo can be pointed at directly by a static host (e.g. GitHub Pages, Netlify, Vercel) with zero build step on the host side. Just remember to run `npm run build` locally and commit the regenerated output before pushing.
- `node_modules/` and `.DS_Store` are gitignored; nothing else should be.

## Compliance (do not skip)
- Malaysian healthcare advertising rules (KKLIU) apply to all Services and Health Education copy: **no claims of guaranteed outcomes**, educational tone only — this is a content/copy constraint, flag it if you see copy drifting into promissory claims.
- Privacy Policy, Terms of Use, Disclaimer, and the KKLIU/MOH approval notice are **client-provided legal content and a launch blocker** — do not fabricate placeholder legal text and ship it as final; mark these pages clearly as pending real copy until the client supplies it.

## Hard Rules
- Do not add sections, features, or content not in `SITEMAP.md`.
- Do not use a page builder or a heavyweight frontend framework/bundler — plain HTML/CSS/JS plus the Tailwind CLI only.
- Do not use `transition-all`.
- Do not use default Tailwind blue/indigo as primary color.
- Do not hand-edit the generated root-level `.html` files — edit `src/pages/` or `src/partials/` and rebuild.
- Do not ship motion that ignores `prefers-reduced-motion`.
- Doctors/services/articles are hardcoded HTML for now (no CMS) — that's a known, accepted deviation from the original client-editing goal, not something to "fix" unilaterally by reintroducing WordPress without the user asking.
