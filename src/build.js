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
    description: "Learn about Curie Oncology's mission, values, Singapore founding, and future plans in precision cancer care.",
    nav: "ABOUT",
  },
  {
    file: "our-doctors.html",
    src: "our-doctors.html",
    title: "Our Doctors - Curie Oncology",
    description: "Meet Curie Oncology's team of consultant clinical oncologists, with a fixed portrait frame that transitions as you scroll.",
    nav: "DOCTORS",
  },
  {
    file: "our-doctors-parallax.html",
    src: "our-doctors-parallax.html",
    title: "Our Doctors (Classic) - Curie Oncology",
    description: "Meet Curie Oncology's team of consultant clinical oncologists in Kuala Lumpur.",
    nav: "DOCTORS",
  },
  {
    file: "our-services.html",
    src: "our-services.html",
    title: "Oncology Services - Curie Oncology",
    description: "Curie Oncology's clinical and diagnostic services, from chemotherapy infusion and radiation therapy to molecular testing and clinical trials.",
    nav: "SERVICES",
  },
  {
    file: "health-education.html",
    src: "health-education.html",
    title: "Health Education - Curie Oncology",
    description: "Patient education guides on cancer recovery, cancer therapy, prevention, and everyday tips for people living with cancer.",
    nav: "HEALTH",
  },
  {
    file: "meet-our-doctors.html",
    src: "meet-our-doctors.html",
    title: "Meet Our Doctors - Curie Oncology",
    description: "Browse Curie Oncology's full team of consultant clinical oncologists and open each doctor's dedicated profile.",
    nav: "DOCTORS",
  },
  {
    file: "dr-lau-fen-nee.html",
    src: "dr-lau-fen-nee.html",
    title: "Dr Lau Fen Nee - Curie Oncology",
    description: "Dr Lau Fen Nee, Senior Consultant Clinical Oncologist at Curie Oncology, specialising in medical oncology and radiotherapy.",
    nav: "DOCTORS",
  },
  {
    file: "dr-ho-gwo-fuang.html",
    src: "dr-ho-gwo-fuang.html",
    title: "Dr. Ho Gwo Fuang - Curie Oncology",
    description: "Dr. Ho Gwo Fuang, Senior Consultant Clinical Oncologist at Curie Oncology, specialising in gastrointestinal cancers.",
    nav: "DOCTORS",
  },
  {
    file: "dr-sow-wen-jen.html",
    src: "dr-sow-wen-jen.html",
    title: "Dr Sow Wen Jen (Jenson) - Curie Oncology",
    description: "Dr Sow Wen Jen (Jenson), Consultant Clinical Oncologist at Curie Oncology, specialising in gastrointestinal cancers.",
    nav: "DOCTORS",
  },
  {
    file: "dato-dr-fuad-ismail.html",
    src: "dato-dr-fuad-ismail.html",
    title: "Dato' Dr Fuad Ismail - Curie Oncology",
    description: "Dato' Dr Fuad Ismail, Visiting Senior Consultant Clinical Oncologist at Curie Oncology, specialising in breast, cervical, and colorectal cancers.",
    nav: "DOCTORS",
  },
  {
    file: "dr-muhammad-azrif.html",
    src: "dr-muhammad-azrif.html",
    title: "Dr Muhammad Azrif - Curie Oncology",
    description: "Dr Muhammad Azrif, Visiting Senior Consultant Clinical Oncologist at Curie Oncology, specialising in radiation oncology.",
    nav: "DOCTORS",
  },
  {
    file: "dr-tan-ai-lian.html",
    src: "dr-tan-ai-lian.html",
    title: "Dr Tan Ai Lian - Curie Oncology",
    description: "Dr Tan Ai Lian, Clinical Oncologist, sees patients at Bagan Specialist Centre in Penang.",
    nav: "DOCTORS",
  },
  {
    file: "dr-yeoh-chit-cheng.html",
    src: "dr-yeoh-chit-cheng.html",
    title: "Dr Yeoh Chit Cheng - Curie Oncology",
    description: "Dr Yeoh Chit Cheng at Curie Oncology - full profile details pending.",
    nav: "DOCTORS",
  },
];

for (const page of pages) {
  let header = headerTemplate;
  for (const key of NAV_KEYS) {
    header = header.replaceAll(`{{NAV_${key}}}`, key === page.nav ? "font-semibold text-sage-700" : "");
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
