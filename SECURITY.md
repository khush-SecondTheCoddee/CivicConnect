# Security Policy: CivicConnect

At **SRO Unincorporated**, we take the privacy and security of citizen data with the highest priority. As an open-source public service, we welcome the community to audit our code to ensure it remains a safe "Digital Postman."

## 1. Supported Versions
Security updates are currently provided for the latest main branch of the repository:
| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | ✅ YES (Active)     |
| < 1.0   | ❌ NO               |

## 2. Reporting a Vulnerability
If you discover a security vulnerability (such as SQL/JSON injection, data exposure, or SMTP relay issues), please **do not** open a public issue on GitHub. Instead, follow these steps:
1. **Email:** Send a detailed report to **srounincorp@gmail.com**.
2. **Details:** Include a proof-of-concept (PoC) and instructions on how to reproduce the bug.
3. **Response:** We will acknowledge your report within 48 hours and work on a fix as a priority.

## 3. Responsible Disclosure
We ask that you give us a reasonable amount of time to fix the issue before making any information public. Since we are a voluntary collective, we do not offer financial bug bounties, but we will provide public credit (with your consent) in our "Security Hall of Fame."

## 4. Privacy Standards
* **Local Storage:** All `users.json` and `complaints.json` data must be protected via `.htaccess`.
* **Zero Logging:** We do not log passwords or full ID numbers in plain text.
* **Encrypted Dispatch:** All emails are sent via TLS-encrypted SMTP.

---
*Securing the Citizen's Voice | SRO Unincorporated*
