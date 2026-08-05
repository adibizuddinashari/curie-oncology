# Sitemap & Content Plan — Curie Oncology Website

Source: `../Curie Website Sitemap.pdf` and `../Sitemap/Curie Oncology Sitemap Flowchart.pdf` (the revised, agency-annotated version — treated here as authoritative since it's cross-checked against oncocare.my and flags compliance/SEO notes the simpler version doesn't).

Legend carried over from the source doc:
- **SHADED/ADDED** — page recommended in addition to the original client outline
- **SEO** — build as its own URL, not folded into a shared page
- **CAREFUL** — copy must stay factual/educational per KKLIU (Malaysian healthcare advertising rules); no outcome guarantees
- 🚫 **BLOCKS LAUNCH** — cannot ship without client-provided content

## On every page (build once, globally)
- Sticky "Book Appointment" CTA
- Language switcher: EN / BM / 中文
- WhatsApp + call button widget

---

## 01 · About Curie
WP structure: static Pages under an "About" parent, or one long page with anchored sections (decide based on final copy length).

| Item | Notes | Content source |
|---|---|---|
| Landing / intro | | — needs new copy |
| Vision & mission | | — needs new copy |
| Why us | | — needs new copy |
| Curie Malaysia | | — needs new copy |
| Singapore founding | Sub-specialists, links out to the SG site | — needs new copy + confirm SG site URL |
| Future | More locations, RT, NM | — needs new copy |
| Newsroom **[ADDED]** | Replaces a vague "ad-hoc" announcements approach with a real hub — build as a WP category/archive of standard Posts | — no existing content |

## 02 · Doctors
WP structure: `Doctor` CPT, one entry = one profile page (`single-doctor.php`), listing page = `archive-doctor.php` (see `DESIGN-BRIEF.md` team-grid pattern).

Fields per doctor: Name & photo, Credentials, Schedule, Languages, Appointment tab (routes to the Contact form).

| Doctor | Photo on file | Bio source |
|---|---|---|
| Dato' Dr Fuad Ismail | ✅ `brand_assets/doctors/Dr-Fuad-Ismail.jpg` | `../localisation/Dato Dr Fuad Ismail_Profile Full_EN.pdf` |
| Dr Muhammad Azrif Ahmad Annuar | ✅ `brand_assets/doctors/Dr-Muhammad-Azrif.jpg` | `../localisation/Dr Muhammad Azrif_Profile Full_EN.pdf`, `../from client/Dr Azrif - Website Write Up 210324 Edited.docx` |
| Dr Lau Fen Nee | ✅ `brand_assets/doctors/Dr-Lau-Fen-Nee.jpg` | `../localisation/Dr Lau Fen_Profile Full_EN.pdf` |
| Dr Sow Wen Jen (Jenson) | ✅ `brand_assets/doctors/Dr-Sow-Wen-Jen.jpg` | `../localisation/Dr Sow Wen Jensen_Profile Full_EN.pdf`, `../from client/Dr Jenson Sow 250324.docx` |
| Dr Ho Gwo Fuang | ✅ `brand_assets/doctors/Dr-Ho-Gwo-Fuang.jpg` | `../localisation/Dr Ho Gwo Fuang_Profile Full_EN.pdf` |

⚠️ Content gap: the reference site shows 7 team members (6 Medical Oncologists, 1 Haematologist, 1 Senior Radiation Oncologist) for the group; only 5 Malaysia-entity doctors have bio material locally. All 5 now have photos on file. Confirm the full current roster with the client before building the team grid.

## 03 · Services
WP structure: static Pages, or a `Service` CPT if the client wants to add/reorder services without a developer.

| Item | Notes |
|---|---|
| Outpatient | |
| Inpatient | |
| Chemo day care | |
| Molecular testing | |
| Research / early access | **CAREFUL** — educational wording only, no outcome claims |
| PAP (Patient Assistance Program) | |
| RT & NM | Future/planned service |

## 04 · Health Education
WP structure: `Health Article` CPT with a `Cancer Type` taxonomy.

| Item | Notes |
|---|---|
| Cancer basics | |
| Cancer types | **SEO** — own URL per type: breast, colorectal, prostate, NPC, head & neck. Build as `Cancer Type` taxonomy terms, not one shared page. |
| Screening | |
| Treatment flow | |
| Patient education | Diet, side effects, elderly care |
| Know your meds | IV and TTH (take-home) medication |

## 05 · Contact Us
WP structure: static Pages; Appointment form and Contact form both need PDPA consent checkboxes (WPForms/Fluent Forms — see `CLAUDE.md`).

| Item | Notes |
|---|---|
| Appointment form | |
| International patients | |
| Insurance & payment **[ADDED]** | Own page — was only implied via PAP in the original brief |
| Career | |
| Location / map | |

## 06 · Ad-hoc (not real pages)
- Pop-up announcements — global overlay/modal, not a URL
- Promotions — campaign banners, likely a homepage/theme-level component
- Campaign banners — same as above

## 07 · Footer / Utility 🚫 blocks launch
Required for a Malaysian medical site; needs explicit client sign-off before launch.

| Item | Status |
|---|---|
| Privacy Policy | 🚫 needs client-provided legal copy |
| Terms of Use | 🚫 needs client-provided legal copy |
| Disclaimer | 🚫 needs client-provided legal copy |
| KKLIU / MOH notice | 🚫 needs approval number from the client's regulatory side — **hard launch blocker** |

---

## Other assets found, not yet mapped to a page
- `../link/Curiegenetics_logo-v2.png` — a related "Curie Genetics" sister brand/service. Not in the current sitemap; confirm with the client whether this needs a mention under Services or About.
- `../working file/Curie Oncology_website.ai` / `.pdf` and `../website_old/Curie Oncology website.pdf` — prior design rounds, kept for historical context only. **Do not use these as the visual reference** — `wp.verzinc.com/curie` is the design reference (see `DESIGN-BRIEF.md`).
- `../localisation/Compiled Translated Malay.pdf`, `Compiled Translation Malay Chinese.pdf`, `Curie Oncology website translation.zip` — source material for the BM/中文 language versions once Polylang/WPML is set up.
- `../ref/home page_2.jpg` — a competitor screenshot (Sunway Medical Centre Penang), kept for sector/competitive context only — not a design reference for this build.
