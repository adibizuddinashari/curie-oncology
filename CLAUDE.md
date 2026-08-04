# CLAUDE.md — Curie Oncology Website (WordPress)

## Project shape
Multi-page WordPress site (not a single-file SPA). The client will edit content themselves post-launch via WP Admin, so every content type that changes regularly (doctor profiles, health education articles, cancer type pages, announcements) must be editable in the CMS — not hardcoded in a template.

Read `SITEMAP.md` before building any page — it maps every page in the site to a WordPress structure (static Page / Custom Post Type / taxonomy term / plugin feature) and flags content that doesn't exist yet.

Read `DESIGN-BRIEF.md` before styling anything — it's the teardown of the design reference plus the real Curie brand values (palette, type, imagery direction).

## Always Do First
- **Invoke the `frontend-design` skill** before writing any frontend code, every session, no exceptions.

## Design Reference
- Primary reference: `https://wp.verzinc.com/curie/` — match its layout, spacing, typography rhythm, and component patterns (header/nav, hero, value cards, team cards, forms, footer, floating WhatsApp widget) as closely as possible.
- Do not copy its color palette literally — use Curie's real brand palette instead (see `DESIGN-BRIEF.md`): sage/mint green as the primary accent (replaces the reference's teal), warm mustard/terracotta as a secondary accent, dark charcoal/navy text on white/off-white.
- Use the real logo and tagline from `brand_assets/logo/` — never a placeholder wordmark.
- Screenshot your output, compare against the reference site, fix mismatches, re-screenshot. Do at least 2 comparison rounds per page template. Stop only when no visible differences remain (beyond the intentional palette swap) or the user says so.
- Do not add sections, features, or content not called for in `SITEMAP.md`. Do not "improve" the reference layout — match it.

## Local WordPress Environment
- Use **LocalWP** (Local by WP Engine) for local development — free, macOS-native, one-click WP instance, and it can generate a shareable live-link preview for client review without a separate staging server.
- Point VS Code at the theme folder inside the LocalWP site (typically `~/Local Sites/<site-name>/app/public/wp-content/themes/<theme-name>/`).
- Always develop against the running LocalWP site URL (e.g. `http://curie-oncology.local`) — never edit theme files and expect to preview them as flat HTML.
- Check LocalWP is running before starting work; don't spin up a second instance.

## Screenshot Workflow
- Screenshot the LocalWP site URL directly (Puppeteer/Playwright pointed at `http://curie-oncology.local`, or the built-in browser preview).
- When comparing to the reference, be specific: "heading is 32px but reference shows ~24px", "card gap is 16px but should be 24px".
- Check: spacing/padding, font size/weight/line-height, colors (exact hex from the brand palette, not the reference's), alignment, border-radius, shadows, image sizing.

## Theme Architecture
- **Custom lightweight theme** — do not use a page builder (Elementor, Bricks, Divi, etc.). A hand-coded theme keeps the markup close to the reference design instead of fighting builder-generated bloat, and gives the client a simpler, more controlled editing surface via ACF fields rather than an open canvas.
- Standard WP template hierarchy: `header.php`, `footer.php`, `page.php` + page-specific templates (`page-services.php`, `page-contact.php`, etc.), `single-doctor.php`, `archive-doctor.php`, `single-health-article.php`, `taxonomy-cancer-type.php`.
- Styling: Tailwind CSS compiled via the **Tailwind CLI** (`npx tailwindcss -i input.css -o style.css --watch`) — no bundler, no build framework. Enqueue the compiled `style.css` in `functions.php`.
- **Custom Post Types**:
  - `Doctor` — fields: name, photo, credentials, schedule/location, languages spoken, appointment link. One page per doctor (per sitemap).
  - `Health Article` — fields: body content, associated `Cancer Type` taxonomy term (breast / colorectal / prostate / NPC / head & neck each get their own URL — see SEO note in `SITEMAP.md`).
  - Newsroom items can be standard WP Posts in a "Newsroom" category — no custom type needed.
- **Custom fields**: ACF (free tier to start; upgrade to Pro only if a Services or Health Education layout genuinely needs repeater/flexible-content fields).
- **Multilingual**: build for Polylang (EN / BM / 中文) by default. WPML is a viable paid alternative if the client wants a more managed translator workflow — that's a business decision for later, not something to block development on now.
- **Forms**: WPForms or Fluent Forms for the appointment form and contact form. Both need a PDPA consent checkbox per the sitemap.
- **Global elements on every page** (build once, into `header.php`/`footer.php`, not per-page): sticky "Book Appointment" CTA, language switcher (EN/BM/中文), WhatsApp + call button widget.

## Brand Assets
- Always check `brand_assets/` before designing. It contains the logo (PNG + JPG), four doctor headshots, and clinic/team photography.
- Use these real assets — do not use placeholder images or invent a wordmark where a real one exists.
- Colors: derive the palette from the logo's sage-green tagline and the clinic's warm mid-century interior tones (see `DESIGN-BRIEF.md`) — do not invent brand colors, and do not default to the reference site's teal.
- Content gap: only 4 of the doctors have headshots on file. Flag missing doctor photos/bios to the client rather than fabricating placeholder headshots for real staff.

## Anti-Generic Guardrails
- **Colors**: no default Tailwind palette (indigo-500, blue-600, etc.). Derive everything from the real sage-green/mustard brand palette.
- **Shadows**: no flat `shadow-md`. Use layered, color-tinted shadows with low opacity.
- **Typography**: pair the logo's serif with a clean sans for UI. Tight tracking (`-0.03em`) on large headings, generous line-height (`1.7`) on body text.
- **Animations**: only animate `transform` and `opacity`. Never `transition-all`. Spring-style easing.
- **Interactive states**: every clickable element needs hover, focus-visible, and active states — this is a healthcare site used by anxious patients; clarity over cleverness.
- **Images**: gradient overlay + color treatment on hero/banner imagery, consistent with the reference site's photo treatment.
- **Depth**: base → elevated → floating layering (cards, sticky CTA, modals), not everything on one plane.

## Compliance (do not skip)
- Malaysian healthcare advertising rules (KKLIU) apply to all Services and Health Education copy: **no claims of guaranteed outcomes**, educational tone only — this is a content/copy constraint, flag it if you see copy drifting into promissory claims.
- Privacy Policy, Terms of Use, Disclaimer, and the KKLIU/MOH approval notice are **client-provided legal content and a launch blocker** — do not fabricate placeholder legal text and ship it as final; mark these pages clearly as pending real copy until the client supplies it.

## Hard Rules
- Do not add sections, features, or content not in `SITEMAP.md`.
- Do not use a page builder plugin.
- Do not use `transition-all`.
- Do not use default Tailwind blue/indigo as primary color.
- Do not hardcode content that the client needs to be able to edit (doctors, articles, announcements) directly into a template — it belongs in a CPT/ACF field.
