# Design Brief — Curie Oncology Website

Two inputs combine here: the **layout/component reference** (`wp.verzinc.com/curie`) and the **real Curie brand** (logo, tagline, clinic photography). Match the former's structure; use the latter's colors, type, and imagery. Do not blend in the reference site's own color choices.

## Reference site teardown — `wp.verzinc.com/curie`

This appears to be an existing build for the wider Curie Oncology group (it already shows the group's multi-location pattern: Singapore, Kuala Lumpur, Cebu, Penang) — treat it as the closest thing to an authoritative layout reference available.

**Header / nav**
- Fixed top bar: logo left, primary nav center/right (Our Team, Services, Locations, Education, Gallery), utility icons (account, cart, search), prominent "BOOK AN APPOINTMENT" button as the clear primary CTA.
- The e-store/cart icon is specific to the group's demo — the Malaysia sitemap has no e-store; omit cart/account icons for this build unless the client asks for one.

**Hero**
- Full-width banner image with a bold, short tagline overlaid ("Cancer Is Not a Death Sentence – Spring Will Come" on the reference). For Curie Malaysia, use the real tagline "Spring Will Come" paired with a headline appropriate to the homepage content in `SITEMAP.md`.

**Homepage sections, in order**
1. **Who We Are** — short positioning bullets (regional oncology specialist, personalized multidisciplinary care).
2. **Mission** — a pull-quote from a senior clinician, plus a secondary "Maximizing Every Chance of Cure" block describing the clinical approach.
3. **Values** — three cards with icon + label (reference uses Compassion First / Innovation with Purpose / Driven by Research). Adapt wording to the Malaysia entity but keep the 3-card icon pattern.
4. **Team showcase** — card grid, grouped by location on the reference (single location here). Each card: circular or square headshot, name (heading weight), title/specialty (lighter subheading), links through to the doctor's own page.
5. **Contact form** — first/last name, contact number, email, message, PDPA agreement checkbox.
6. **Footer** — logo, sitemap link columns, quick links (Terms & Conditions, PDPA), contact numbers, social icons (Facebook, Instagram, YouTube), copyright line.
7. **Floating WhatsApp widget**, bottom corner, persistent across the site.

**Doctor/team listing page** (`/our-team/` on the reference)
- Card grid grouped by section heading (locations on the reference; could be by specialty/department here if that reads better for a single-location site).
- No filtering UI — everything lists at once.
- Each card is a link through to a full doctor profile page.

**Typography (structure, not literal fonts)**
- Clear heading hierarchy: name/title-level headings noticeably smaller and lighter-weight than page-level headings.
- Section dividers use a mid-weight heading style, not a heavy banner treatment — keep page headers restrained.

**Buttons / components**
- Primary CTA button style repeats consistently everywhere ("Book Appointment") — same visual treatment site-wide, often paired with an arrow icon.
- Rounded-corner cards with a hover state for team members and value props.
- Form fields: clearly labeled, generous spacing, single-column on mobile.

## Real Curie brand identity

**Logo** (`brand_assets/logo/180220_Curie_Logo_Pathed-02.png`)
- A fine-line illustrated silhouette of Marie Curie's profile above a tracked-out serif wordmark "CURIE ONCOLOGY", with the tagline "SPRING WILL COME" set below in small tracked caps in a **sage/mint green**. This same sage-green appears on the clinic's actual glass entrance signage — it's the brand's real accent color, not an arbitrary pick.

**Color direction**
- Base: white / off-white, matching the clinic's clean glass-and-white architecture.
- Primary text: dark charcoal or navy (not pure black) — matches the reference site's text treatment and reads calmer for a healthcare audience.
- **Primary accent: sage/mint green** (sampled from the logo tagline and clinic signage) — replaces the reference site's teal/turquoise in buttons, links, highlights, icons.
- **Secondary accent: warm mustard-yellow and terracotta/red-orange** — sampled from the clinic's actual lounge furniture (mustard and red-orange accent chairs against walnut wood). Use sparingly: icon fills, small highlight details, illustration accents — not as a competing primary color.
- Avoid a cold/clinical all-blue palette; the real interior photography is warm and human, and the palette should follow that rather than generic medical teal-and-white.

**Typography direction**
- Pair a serif display face (echoing the logo's serif wordmark) for major headings/pull-quotes with a clean, highly legible sans-serif for UI, nav, and body copy — this is a site read by patients and families under stress; body text legibility matters more than personality.
- Tight tracking on large serif headings; generous line-height (1.6–1.7) on body text.

**Photography / imagery style**
- Use real clinic and team photography (see `brand_assets/clinic/` and `brand_assets/doctors/`) over stock imagery wherever available.
- The real interior is warm, human, art-filled (framed prints, orchids, walnut furniture) rather than sterile — carry that warmth into hero imagery, section backgrounds, and photo treatment (avoid harsh cold-blue color grading on images).
- Doctor headshots should be presented consistently (same crop ratio/background treatment) even though the source photos currently vary in style — flag this for a proper photo shoot or consistent editing pass before launch.

## Component inventory to build
Header/nav with sticky CTA · hero banner · bullet/positioning block · pull-quote block · 3-up icon value cards · doctor/team card (grid + single profile template) · service description blocks · health-education article template · cancer-type taxonomy landing template · appointment/contact form with PDPA checkbox · footer with multi-column sitemap links · floating WhatsApp/call widget · language switcher (EN/BM/中文).
