<?php
/**
 * Database Seeder
 * Run once: php seed.php
 *
 * Creates SQLite database + seeds data from original JS arrays.
 * CLI only — cannot be executed via browser.
 */

if (PHP_SAPI !== 'cli') {
    exit("This script can only be run from the command line.\n");
}

// Load bootstrap for constants
define('ROOT_PATH', __DIR__);
define('DATA_PATH', __DIR__ . '/data');
define('APP_PATH', __DIR__ . '/app');

require_once APP_PATH . '/Core/Database.php';

$db = Database::getInstance();

echo "Setting up database...\n";

// Create tables
$db->exec("
CREATE TABLE IF NOT EXISTS visitors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip_address TEXT NOT NULL,
    user_agent TEXT,
    country TEXT DEFAULT 'ID',
    visited_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT,
    service TEXT,
    budget TEXT,
    timeline TEXT,
    message TEXT NOT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    description TEXT,
    image TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS tools (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    category_label TEXT,
    icon TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS faqs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS testimonials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    role TEXT NOT NULL,
    avatar TEXT NOT NULL,
    text TEXT NOT NULL,
    rating INTEGER DEFAULT 5,
    sort_order INTEGER DEFAULT 0
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    number TEXT NOT NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS gallery (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    image TEXT NOT NULL,
    description TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS showcase_projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    image TEXT NOT NULL,
    link TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS certificates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    credential_id TEXT,
    credential_link TEXT,
    image TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

echo "Tables created.\n";

// Seed Projects
$projects = [
    ['Deva', 'website', 'Dark portfolio for a full-stack developer and creative technologist.', 'assets/image/projects/website/project-1.webp', 1],
    ['Mekatron', 'website', 'Sci-fi combat unit dashboard with real-time stat comparison.', 'assets/image/projects/website/project-2.webp', 2],
    ['Xilham Dashboard', 'website', 'eCommerce admin dashboard with sales overview and analytics.', 'assets/image/projects/website/project-3.webp', 3],
    ['Maun', 'website', 'Landing page for a global collective of digital nomads.', 'assets/image/projects/website/project-4.webp', 4],
    ['Hiko', 'website', 'Travel landing page with immersive full-screen hero.', 'assets/image/projects/website/project-5.webp', 5],
    ['IMT Coffee', 'website', 'Restaurant website with interactive menu carousel.', 'assets/image/projects/website/project-6.webp', 6],
    ['Alfiz Ilham v1', 'website', 'First personal portfolio in Indonesian.', 'assets/image/projects/website/project-7.webp', 7],
    ['Luminous Living', 'website', 'Glassmorphism interior design showcase.', 'assets/image/projects/website/project-8.webp', 8],
    ['Niswatul Chaira', 'website', 'Personal branding site for a counseling professional.', 'assets/image/projects/website/project-9.webp', 9],
    ['TypeDash', 'website', 'Typing speed game with WPM and accuracy tracker.', 'assets/image/projects/website/project-10.webp', 10],
    ['Card UI Template', 'website', 'Minimal UI card component template.', 'assets/image/projects/website/project-11.webp', 11],
    ['Alfiz Travel', 'website', 'Islamic travel landing page featuring Aceh and Mecca.', 'assets/image/projects/website/project-12.webp', 12],
    ['Blade Runner', 'website', 'Cyberpunk noir animated landing page.', 'assets/image/projects/website/project-13.webp', 13],
    ['Dev Hero', 'website', 'Developer portfolio hero with floating code animation.', 'assets/image/projects/website/project-14.webp', 14],
    ['Cosmos', 'website', 'Space exploration landing page with animated planets.', 'assets/image/projects/website/project-15.webp', 15],
    ['Nagita Restaurant', 'website', 'Modern restaurant website with menu section.', 'assets/image/projects/website/project-16.webp', 16],
    ['UI/UX Portfolio', 'website', 'Light and dark mode product designer portfolio.', 'assets/image/projects/website/project-17.webp', 17],
    ['Auto Prime', 'website', 'Premium car dealership landing page.', 'assets/image/projects/website/project-18.webp', 18],
    ['Urban Bliss', 'website', 'Luxury real estate landing page.', 'assets/image/projects/website/project-19.webp', 19],
    ['Korea Tour', 'design', 'Travel poster for Seoul and Nami Island tour.', 'assets/image/projects/design/project-20.webp', 20],
    ['Xi\'an & Chongqing', 'design', 'Tour package poster for China destinations.', 'assets/image/projects/design/project-21.webp', 21],
    ['Tour 3 Negara', 'design', 'Multi-country tour poster covering Malaysia, Singapore, Thailand.', 'assets/image/projects/design/project-22.webp', 22],
    ['Phuket & Maya Bay', 'design', 'Promo trip poster for Thailand island tour.', 'assets/image/projects/design/project-23.webp', 23],
    ['Open Donasi', 'design', 'Donation campaign poster for Aceh flood relief.', 'assets/image/projects/design/project-24.webp', 24],
    ['Umrah Plus Turki', 'design', 'Premium umrah package poster.', 'assets/image/projects/design/project-25.webp', 25],
    ['Pusaka Peduli', 'design', 'Horizontal banner for Aceh flood charity campaign.', 'assets/image/projects/design/project-26.webp', 26],
    ['Paket Umrah', 'design', 'Umrah package poster with tiered pricing.', 'assets/image/projects/design/project-27.webp', 27],
    ['Open Trip 3 Negara', 'design', 'Open trip flyer with illustrated landmark silhouettes.', 'assets/image/projects/design/project-28.webp', 28],
    ['An-Nazi\'at', 'calligraphy', 'Illuminated Quran page with colorful floral border.', 'assets/image/projects/calligraphy/project-29.webp', 29],
    ['Spiral Composition', 'calligraphy', 'Red ink calligraphy in spiral form.', 'assets/image/projects/calligraphy/project-30.webp', 30],
    ['Kufi Geometric', 'calligraphy', 'Black and red ink with kufi-style geometric border.', 'assets/image/projects/calligraphy/project-31.webp', 31],
    ['Blue Jali', 'calligraphy', 'Large-scale white on blue Jali Diwani script.', 'assets/image/projects/calligraphy/project-32.webp', 32],
    ['Neon Circle', 'calligraphy', 'Rainbow neon calligraphy in circular composition.', 'assets/image/projects/calligraphy/project-33.webp', 33],
    ['Neon Teardrop', 'calligraphy', 'Rainbow neon calligraphy shaped as a teardrop.', 'assets/image/projects/calligraphy/project-34.webp', 34],
    ['Neon Whale', 'calligraphy', 'Arabic calligraphy forming the shape of a whale.', 'assets/image/projects/calligraphy/project-35.webp', 35],
    ['Name Boards', 'calligraphy', 'Custom name calligraphy boards with rainbow ink.', 'assets/image/projects/calligraphy/project-36.webp', 36],
    ['Naskh Lined', 'calligraphy', 'Clean Naskh script calligraphy in ruled format.', 'assets/image/projects/calligraphy/project-37.webp', 37],
    ['Al-Qalam', 'calligraphy', 'Illuminated Quran page with multicolor floral border.', 'assets/image/projects/calligraphy/project-38.webp', 38],
    ['An-Najm', 'calligraphy', 'Green-themed illuminated calligraphy.', 'assets/image/projects/calligraphy/project-39.webp', 39],
    ['Al-A\'la', 'calligraphy', 'Blue-dominant illuminated page with circular ornament.', 'assets/image/projects/calligraphy/project-40.webp', 40],
    ['Neon Flame', 'calligraphy', 'Single flame-shaped neon calligraphy.', 'assets/image/projects/calligraphy/project-41.webp', 41],
    ['Diwani Illuminated', 'calligraphy', 'Horizontal Diwani script with vivid floral illumination.', 'assets/image/projects/calligraphy/project-42.webp', 42],
    ['Exhibition Display', 'calligraphy', 'Framed calligraphy works at a public exhibition.', 'assets/image/projects/calligraphy/project-43.webp', 43],
    ['Ayat Kursi Blue', 'calligraphy', 'Full Ayat Kursi in white on royal blue.', 'assets/image/projects/calligraphy/project-44.webp', 44],
    ['Ar-Rahman', 'calligraphy', 'Colorful illuminated page with circular composition.', 'assets/image/projects/calligraphy/project-45.webp', 45],
    ['Yasin', 'calligraphy', 'Brown-toned illuminated Quran page.', 'assets/image/projects/calligraphy/project-46.webp', 46],
];

// Ensure 'image' column exists (lazy migration for older databases)
$hasImage = false;
foreach ($db->fetchAll("PRAGMA table_info(services)") as $col) {
    if ($col['name'] === 'image') {
        $hasImage = true;
        break;
    }
}
if (!$hasImage) {
    $db->exec("ALTER TABLE services ADD COLUMN image TEXT");
    echo "Added 'image' column to services table.\n";
}

// Clear existing data
$db->exec("DELETE FROM projects");

$stmt = $db->getPdo()->prepare("INSERT INTO projects (name, category, description, image, sort_order) VALUES (?, ?, ?, ?, ?)");
foreach ($projects as $p) {
    $stmt->execute($p);
}
echo "Seeded " . count($projects) . " projects.\n";

// Seed Tools
$tools = [
    // languages (7)
    ['HTML', 'languages', 'Markup', 'assets/image/icons/fullstack/html5.svg', 1],
    ['CSS', 'languages', 'Styling', 'assets/image/icons/fullstack/css3.svg', 2],
    ['JavaScript', 'languages', 'Language', 'assets/image/icons/fullstack/javascript.svg', 3],
    ['TypeScript', 'languages', 'Language', 'assets/image/icons/fullstack/typescript.svg', 4],
    ['PHP', 'languages', 'Language', 'assets/image/icons/fullstack/php.svg', 5],
    ['Python', 'languages', 'Language', 'assets/image/icons/fullstack/python.svg', 6],
    ['C#', 'languages', 'Language', 'assets/image/icons/fullstack/csharp.svg', 7],
    // frontend (12)
    ['Sass', 'frontend', 'Preprocessor', 'assets/image/icons/fullstack/sass.svg', 8],
    ['Dart', 'frontend', 'Language', 'assets/image/icons/fullstack/dart.svg', 9],
    ['React', 'frontend', 'Library', 'assets/image/icons/fullstack/react.svg', 10],
    ['Next.js', 'frontend', 'Framework', 'assets/image/icons/fullstack/nextjs.svg', 11],
    ['Redux', 'frontend', 'State Mgmt', 'assets/image/icons/fullstack/redux.svg', 12],
    ['Tailwind CSS', 'frontend', 'Styling', 'assets/image/icons/fullstack/tailwindcss.svg', 13],
    ['Bootstrap', 'frontend', 'Framework', 'assets/image/icons/fullstack/bootstrap.svg', 14],
    ['jQuery', 'frontend', 'Library', 'assets/image/icons/fullstack/jquery.svg', 15],
    ['D3.js', 'frontend', 'Visualization', 'assets/image/icons/fullstack/d3.svg', 16],
    ['Vite', 'frontend', 'Build Tool', 'assets/image/icons/fullstack/vite.svg', 17],
    ['Framer', 'frontend', 'Animation', 'assets/image/icons/fullstack/framer.svg', 18],
    ['Flutter', 'frontend', 'Framework', 'assets/image/icons/fullstack/flutter.svg', 19],
    // backend (4)
    ['Node.js', 'backend', 'Runtime', 'assets/image/icons/fullstack/nodejs.svg', 20],
    ['Express.js', 'backend', 'Framework', 'assets/image/icons/fullstack/express.svg', 21],
    ['REST API', 'backend', 'API', 'assets/image/icons/fullstack/rest-api.svg', 22],
    ['MCP', 'backend', 'Protocol', 'assets/image/icons/ai/mcp.svg', 23],
    // database (5)
    ['MongoDB', 'database', 'Database', 'assets/image/icons/fullstack/mongodb.svg', 24],
    ['NeonDB', 'database', 'Database', 'assets/image/icons/fullstack/neon.svg', 25],
    ['PostgreSQL', 'database', 'Database', 'assets/image/icons/fullstack/postgresql.svg', 26],
    ['MySQL', 'database', 'Database', 'assets/image/icons/fullstack/mysql.svg', 27],
    ['SQL', 'database', 'Query', 'assets/image/icons/fullstack/sql.svg', 28],
    // devops (6)
    ['Git', 'devops', 'Version Control', 'assets/image/icons/fullstack/git.svg', 29],
    ['GitHub', 'devops', 'Platform', 'assets/image/icons/fullstack/github.svg', 30],
    ['Docker', 'devops', 'Container', 'assets/image/icons/fullstack/docker.svg', 31],
    ['Linux', 'devops', 'Terminal', 'assets/image/icons/fullstack/linux.svg', 32],
    ['Railway', 'devops', 'Deploy', 'assets/image/icons/platform/railway.svg', 33],
    ['Vercel', 'devops', 'Deploy', 'assets/image/icons/platform/vercel.svg', 34],
    // ai-ml (9)
    ['Claude', 'ai-ml', 'AI Platform', 'assets/image/icons/ai/claude.svg', 36],
    ['OpenRouter', 'ai-ml', 'AI Platform', 'assets/image/icons/ai/openrouter.svg', 37],
    ['Amazon Bedrock', 'ai-ml', 'AI Platform', 'assets/image/icons/ai/aws-bedrock.svg', 38],
    ['Vertex AI', 'ai-ml', 'AI Platform', 'assets/image/icons/ai/vertex-ai.svg', 39],
    ['TensorFlow', 'ai-ml', 'ML Framework', 'assets/image/icons/ai/tensorflow.svg', 41],
    ['NumPy', 'ai-ml', 'Data Science', 'assets/image/icons/ai/numpy.svg', 42],
    ['Pandas', 'ai-ml', 'Data Analysis', 'assets/image/icons/ai/pandas.svg', 43],
    ['Scikit-learn', 'ai-ml', 'ML Library', 'assets/image/icons/ai/scikit-learn.svg', 44],
    ['NVIDIA API', 'ai-ml', 'AI Platform', 'assets/image/icons/ai/nvidia.svg', 45],
    // design (5)
    ['Photoshop', 'design', 'Photo Edit', 'assets/image/icons/design/photoshop.svg', 49],
    ['Lightroom', 'design', 'Photo Edit', 'assets/image/icons/design/lightroom.svg', 50],
    ['CorelDRAW', 'design', 'Vector', 'assets/image/icons/design/coreldraw.svg', 51],
    ['Canva', 'design', 'Graphic Design', 'assets/image/icons/design/canva.svg', 52],
    ['CalliPro', 'design', 'Calligraphy', 'assets/image/icons/design/callipro.svg', 53],
    // tools (8)
    ['Trae', 'tools', 'AI Coding', 'assets/image/icons/platform/trae.svg', 35],
    ['Google Colab', 'tools', 'AI Platform', 'assets/image/icons/ai/google-colab.svg', 40],
    ['Codex', 'tools', 'AI Coding', 'assets/image/icons/ai/codex.svg', 46],
    ['Obsidian', 'tools', 'Note-taking', 'assets/image/icons/ai/obsidian.svg', 47],
    ['OpenCode', 'tools', 'AI Coding', 'assets/image/icons/ai/opencode.svg', 48],
    ['Android Studio', 'tools', 'IDE', 'assets/image/icons/platform/android-studio.svg', 54],
    ['Scratch', 'tools', 'Education', 'assets/image/icons/platform/scratch.svg', 55],
    ['n8n', 'tools', 'Automation', 'assets/image/icons/platform/n8n.svg', 56],
];

$db->exec("DELETE FROM tools");

$stmt = $db->getPdo()->prepare("INSERT INTO tools (name, category, category_label, icon, sort_order) VALUES (?, ?, ?, ?, ?)");
foreach ($tools as $t) {
    $stmt->execute($t);
}
echo "Seeded " . count($tools) . " tools.\n";

// Seed Services
$services = [
    ['01', 'Full-Stack Web Development', 'Production websites and web apps — React/Next.js on the front, Node.js/Python/PHP on the back, shipped end-to-end.', 'assets/image/services/service-1.webp', 1],
    ['02', 'AI-Integrated Applications', 'Apps with an AI assistant built directly into the product — OpenRouter/Claude-powered, context-aware, production-ready.', 'assets/image/services/service-2.webp', 2],
    ['03', 'Workflow Automation', 'n8n automations that connect your tools — webhook flows, LLM message filtering, and hands-off data routing.', 'assets/image/services/service-3.webp', 3],
    ['04', 'API & Database Engineering', 'REST APIs and database design with live sync — Neon PostgreSQL, OAuth wiring, Excel/PDF/JSON export pipelines.', 'assets/image/services/service-4.webp', 4],
    ['05', 'Tech Consultation', 'Advice on stack, architecture, and AI integration strategy — Claude API, AWS Bedrock, Vertex AI, and beyond.', 'assets/image/services/service-5.webp', 5],
];

$db->exec("DELETE FROM services");

$stmt = $db->getPdo()->prepare("INSERT INTO services (number, title, description, image, sort_order) VALUES (?, ?, ?, ?, ?)");
foreach ($services as $s) {
    $stmt->execute($s);
}
echo "Seeded " . count($services) . " services.\n";

// Seed Testimonials
$testimonials = [
    ['Ata Sidqi', 'Batch Representative of Pusaka Jeumala', 'assets/image/people/atasidqi.webp', 'Pusaka App made managing our participants so much easier. Organizing, tracking, everything — it\'s just faster now, and we make way fewer mistakes.', 5, 1],
    ['Niswatul Chaira, M.Pd', 'School Counselor at Jeumala Amal', 'assets/image/people/niswatulchaira.webp', 'The portfolio website looks really professional. What I like most is I can update the content myself, straight from the site — no coding needed.', 5, 2],
    ['Imam Fuadi', 'Travel Agency Owner of Imam Travel', 'assets/image/people/imamfuadi.webp', 'The brochures and posters came out great. They look sharp and professional, and people actually notice them — the message gets across clearly too.', 5, 3],
];

$db->exec("DELETE FROM testimonials");

$stmt = $db->getPdo()->prepare("INSERT INTO testimonials (name, role, avatar, text, rating, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($testimonials as $t) {
    $stmt->execute($t);
}
echo "Seeded " . count($testimonials) . " testimonials.\n";

// Seed Gallery
$gallery = [
    ['assets/image/gallery/photo-1.webp', 'Sharing about AI development at the graduation day of Generation 34 Jeumala Amal', 1],
    ['assets/image/gallery/photo-2.webp', 'Visit to Padang contingent at KPMN 2024, Cibubur', 2],
    ['assets/image/gallery/photo-3.webp', 'Together with Calligraphy Champion I at MTR Meulaboh 2025', 3],
    ['assets/image/gallery/photo-4.webp', 'Aceh contingent at Taman Lebah Cibubur, KPMN 2024', 4],
    ['assets/image/gallery/photo-5.webp', 'With the activity guide at Taman Lebah Cibubur, KPMN 2024', 5],
    ['assets/image/gallery/photo-6.webp', 'Aceh contingent at Bundaran HI Jakarta, KPMN 2024', 6],
    ['assets/image/gallery/photo-7.webp', 'Dayah Jeumala Amal representatives in the Aceh contingent at KPMN 2024', 7],
    ['assets/image/gallery/photo-8.webp', 'With the Vice Regent of Pidie Jaya at the MTR 2025 send-off ceremony in Meulaboh', 8],
    ['assets/image/gallery/photo-9.webp', 'Jakarta City Tour - Monas Tower, KPMN 2024', 9],
    ['assets/image/gallery/photo-10.webp', 'MTR 2025 participants in front of Meulaboh Grand Mosque', 10],
    ['assets/image/gallery/photo-11.webp', 'Group photo after the MTR Meulaboh 2026 winners announcement', 11],
    ['assets/image/gallery/photo-12.webp', 'With Pidie Jaya contingent guides after MTR Meulaboh 2026', 12],
    ['assets/image/gallery/photo-13.webp', 'With Bunda Pramuka Pidie Jaya after winning Calligraphy 3rd Place', 13],
    ['assets/image/gallery/photo-14.webp', 'Jeumala Amal student volunteer relief effort for Pidie Jaya flood victims', 14],
    ['assets/image/gallery/photo-15.webp', 'Breaking fast together with NKT Ikhwan Darul Lughah Al-Arabiyah', 15],
    ['assets/image/gallery/photo-16.webp', 'Farewell moment of class XII-4 Jeumala Amal', 16],
];

$db->exec("DELETE FROM gallery");

$stmt = $db->getPdo()->prepare("INSERT INTO gallery (image, description, sort_order) VALUES (?, ?, ?)");
foreach ($gallery as $g) {
    $stmt->execute($g);
}
echo "Seeded " . count($gallery) . " gallery items.\n";

// Seed FAQs
$faqs = [
    ['pricing', 'What is the cost of your services?', 'It depends on the scope of your project. Contact me with your requirements and I\'ll provide a tailored quote.', 1],
    ['process', 'How much time is typically needed to finish a project?', 'A landing page takes around 1.5 days using my AI-directed workflow. Larger projects typically take 1 to 2 weeks depending on complexity.', 2],
    ['services', 'How does your revision policy work?', 'Minor revisions such as color, font, or content changes are included in the initial price. Structural changes like adding new pages are charged separately based on the request.', 3],
    ['services', 'Can you work with my existing brand guidelines?', 'Yes. I adapt my design and development workflow to match your existing brand identity and guidelines.', 4],
    ['services', 'Do you offer website maintenance after launch?', 'As a freelancer, yes. Maintenance is available at an additional cost. If you hire me full-time as a remote employee, maintenance is included as part of the role.', 5],
    ['contact', 'Can you handle projects from outside Indonesia?', 'Yes. I work with clients globally and offer the same quality of service regardless of location.', 6],
    ['general', 'What sets you apart from other designers and developers?', 'I combine full-stack development, AI workflow engineering, and Arabic calligraphy art in one workflow. You get a developer who designs, codes, and deploys — not just one piece of the puzzle.', 7],
    ['general', 'What tools and tech stack do you use?', 'For frontend, I use React and Next.js. For backend, I work with Node.js, Python, and PostgreSQL. I also build AI workflows and ML models using Claude API, TensorFlow, and cloud platforms like AWS Bedrock and Vertex AI.', 8],
    ['process', 'What does your working process look like?', 'I start by understanding your needs, then architect the solution — from system design to development and deployment. AI accelerates the workflow, but quality and communication stay hands-on.', 9],
    ['contact', 'Are you open to full-time remote work?', 'Yes. I am actively looking for full-time remote opportunities as a Software & AI Engineer or full-stack developer.', 10],
    ['services', 'Can you provide design only, without building the website?', 'I do not use design tools like Figma or Framer. Instead, I design directly in React, which means the design is live and interactive from day one. Revisions are done in the same workflow.', 11],
    ['services', 'Can I order Arabic calligraphy online?', 'At the moment, I do not accept online calligraphy orders. My calligraphy work is showcased as part of my portfolio only.', 12],
    ['pricing', 'What is your payment structure?', 'For larger projects, I require a deposit before work begins. Payment terms are discussed and agreed upon before the project starts.', 13],
    ['services', 'Can you integrate features like payment gateway, database, or backend?', 'Yes. I have experience building full-stack applications with React, Node.js, Python, and PostgreSQL — including payment gateways, REST APIs, and database design.', 14],
    ['contact', 'What language do you communicate in with international clients?', 'I communicate in Arabic with clients from Arab countries, and in English with clients from the US, UK, and other English-speaking regions.', 15],
];

$db->exec("DELETE FROM faqs");

$stmt = $db->getPdo()->prepare("INSERT INTO faqs (category, question, answer, sort_order) VALUES (?, ?, ?, ?)");
foreach ($faqs as $f) {
    $stmt->execute($f);
}
echo "Seeded " . count($faqs) . " FAQs.\n";

// Certificates table (created lazily, no seed data — admin uploads manually)

echo "\nSetup complete! Database is ready.\n";
echo "Visit http://localhost/alfizilham to view the portfolio.\n";
