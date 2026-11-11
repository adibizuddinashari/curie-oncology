const fs = require("fs");
const path = require("path");

const ROOT = path.join(__dirname, "..");
const PARTIALS = path.join(__dirname, "partials");
const PAGES = path.join(__dirname, "pages");

const read = (p) => fs.readFileSync(p, "utf8");

const shell = read(path.join(PARTIALS, "shell.html"));
const footer = read(path.join(PARTIALS, "footer.html"));
const headerTemplate = read(path.join(PARTIALS, "header.html"));

const NAV_KEYS = ["HOME", "ABOUT", "DOCTORS", "SERVICES", "HEALTH", "CONTACT"];

const pages = [
  {
    file: "index.html",
    src: "home.html",
    title: "Curie Oncology - Compassionate Care, Precision Oncology",
    description: "Curie Oncology is a boutique oncology practice in Kuala Lumpur offering precision, multidisciplinary cancer care. Spring Will Come.",
    nav: "HOME",
  },
  {
    file: "about.html",
    src: "about.html",
    title: "About Us - Curie Oncology",
    description: "Curie Oncology's story, philosophy of personalised cancer care, and its network with Curie Singapore.",
    nav: "ABOUT",
  },
  {
    file: "our-services.html",
    src: "our-services.html",
    title: "Oncology Services - Curie Oncology",
    description: "Curie Oncology's clinical and diagnostic services, from chemotherapy infusion and CAR T cell therapy to molecular testing and clinical trials.",
    nav: "SERVICES",
  },
  {
    file: "health-education.html",
    src: "health-education.html",
    title: "Resource Centre - Curie Oncology",
    description: "Patient education guides on cancer recovery, cancer therapy, prevention, and everyday tips for people living with cancer.",
    nav: "HEALTH",
  },
  {
    file: "contact-us.html",
    src: "contact-us.html",
    title: "Contact Us - Curie Oncology",
    description: "Contact Curie Oncology in Kuala Lumpur or Penang: clinic addresses, phone numbers, email, operating hours and a message form.",
    nav: "CONTACT",
  },
  {
    file: "meet-our-doctors.html",
    src: "meet-our-doctors.html",
    title: "Meet Our Doctors - Curie Oncology",
    description: "Meet Curie Oncology Malaysia's consultants in medical oncology and clinical oncology in Klang Valley and Penang.",
    nav: "DOCTORS",
  },
  {
    file: "dr-lau-fen-nee.html",
    src: "dr-lau-fen-nee.html",
    title: "Dr Lau Fen Nee - Curie Oncology",
    description: "Dr Lau Fen Nee, Senior Consultant Clinical Oncologist at Curie Oncology, Klang Valley. Former Head of the Department of Radiotherapy and Oncology at the National Cancer Institute.",
    nav: "DOCTORS",
  },
  {
    file: "dr-ho-gwo-fuang.html",
    src: "dr-ho-gwo-fuang.html",
    title: "Dr Ho Gwo Fuang - Curie Oncology",
    description: "Dr Ho Gwo Fuang, Senior Consultant Clinical Oncologist at Curie Oncology, Klang Valley. Professor and clinical oncologist.",
    nav: "DOCTORS",
  },
  {
    file: "dr-sow-wen-jen.html",
    src: "dr-sow-wen-jen.html",
    title: "Dr Jenson Sow Wen Jen - Curie Oncology",
    description: "Dr Jenson Sow Wen Jen, Consultant Clinical Oncologist at Curie Oncology, Klang Valley. Special interest in GI cancers.",
    nav: "DOCTORS",
  },
  {
    file: "dr-tan-ai-lian.html",
    src: "dr-tan-ai-lian.html",
    title: "Dr Tan Ai Lian - Curie Oncology",
    description: "Dr Tan Ai Lian, Clinical Oncologist at Curie Oncology, Penang. Focus: lung, head and neck and gastrointestinal cancers.",
    nav: "DOCTORS",
  },
  {
    file: "dr-yeoh-chit-cheng.html",
    src: "dr-yeoh-chit-cheng.html",
    title: "Dr Yeoh Chit Cheng - Curie Oncology",
    description: "Dr Yeoh Chit Cheng, Consultant Medical Oncologist at Curie Oncology, Klang Valley. Focus: acute oncology, lymphoma, skin cancer, gynae cancers and cancer of unknown primary.",
    nav: "DOCTORS",
  },
];

for (const page of pages) {
  let header = headerTemplate;
  for (const key of NAV_KEYS) {
    header = header.replaceAll(`{{NAV_${key}}}`, key === page.nav ? "!text-forest" : "");
  }

  const content = read(path.join(PAGES, page.src));

  const html = shell
    .replace("{{TITLE}}", page.title)
    .replace("{{DESCRIPTION}}", page.description)
    .replace("{{HEADER}}", header)
    .replace("{{CONTENT}}", content)
    .replace("{{FOOTER}}", footer);

  fs.writeFileSync(path.join(ROOT, page.file), html);
  console.log(`built ${page.file}`);
}
