# Adoption & Installation Guide

**CivicConnect** is designed to be highly portable. As an unincorporated public service, we encourage other collectives to deploy their own instances of this gateway to serve local communities.

## 📋 Prerequisites
* **Hosting:** Any PHP-enabled web server (e.g., InfinityFree, 000Webhost, or a private VPS).
* **PHP Version:** 8.0 or higher.
* **SMTP Credentials:** A Gmail App Password or an official departmental SMTP relay.

## 🚀 Quick Start
1. **Clone the Source:** Download the repository to your `/htdocs` or `/public_html` folder.
2. **Configure SMTP:** Open `config.php` and enter your Mail Server details.
3. **Set Permissions:** Ensure the `/data` folder is writable (`755` or `777`) so the JSON database can function.
4. **Secure Data:** Create an `.htaccess` file inside `/data` containing `Deny from all` to protect citizen privacy.

## 🛠️ Customization
* **Local Directory:** Edit `directory.json` to include your local Municipal Corporation, Block Office, or District Collectorate emails.
* **Styling:** Modify `style.css` to add your collective's branding or local language support.
* **Demo Mode:** Keep `define('DEMO', 1);` active during testing to ensure emails route to your test inbox before going live.

## 🆘 Support
As an unincorporated entity, we do not provide 24/7 technical support. However, deployment queries can be directed to:
* **Email:** srounincorp@gmail.com
* **GitHub Issues:** [Insert Link to your Repo]

---
*Empowering Local Governance through Open Technology.*
