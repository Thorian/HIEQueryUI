# Reporting security issues

Report vulnerabilities privately to the repository owner; use GitHub private vulnerability reporting if enabled. Avoid including customer information, credentials, or production sessions in an issue.

Include the affected revision, a minimal independent reproduction, the relevant rendering/event mode, and the PHP/browser versions. The current release candidate has not received a complete external security audit.

Applications remain responsible for authentication, authorization, database operations, trusted HTML, and session cookie/storage policy. A request token does not authorize a business action. Never deserialize client input or treat Base64 as validation or encryption.
