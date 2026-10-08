EXCEL PACES WEBSITE + CMS  (PHP 8 / MySQL — runs on Hostinger shared hosting)

DEPLOY (10 minutes)
1. hPanel > Databases > MySQL Databases: create a database + user. Note the three values.
2. hPanel > File Manager (or FTP) > public_html: upload everything in this folder (excluding README.txt if you like).
3. Visit https://yourdomain/install.php, enter the DB details and your admin e-mail/password. Done.
4. Delete install.php from the server afterwards.
5. hPanel > SSL: enable free SSL, then force HTTPS.
6. Sign in at https://yourdomain/admin/ and:
   - Site Settings: upload the Excel Paces logo, favicon, check colours, paste Google Maps embed code
   - Footer Logos: upload KIMSHEALTH / Aster DM / Aster / CARE / evercare logos
   - Courses & Dates: add the next course (drives the home-page countdown + Apply form)
   - Faculty: add photos/bios.

WHAT THE CMS EDITS: pages (rich-text editor), home banners, courses & dates, highlight cards / PACES stations,
faculty, testimonials, gallery (bulk upload), footer logos, menu, site settings/colours, admin users;
applications & contact messages inbox (status tracking + CSV export + e-mail notification).

BACKUP: hPanel > Backups, plus download the /uploads folder and export the database from phpMyAdmin.
