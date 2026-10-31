# mitigata-php — Operation Black Friday (CTF)

A deliberately insecure **PHP** web application, delivered as a capture-the-flag challenge.

No vulnerability list. No hints. No write-up. That's the point — **find the flaws, exploit
them, and capture the flags.**

## Objective
Hidden across the application are flags in the form `FLAG{...}`. Recover them all.
The app is built as a themed, chapter-by-chapter mission — each area hides something.

## Run it
```bash
docker build -t mitigata-php .
docker run --rm -p 8081:80 mitigata-php
# then open http://localhost:8081
```

## Rules of engagement
- **Authorized security training / CTF use only.**
- This application is *intentionally* exploitable. Run it **only on an isolated host or VM** —
  never on a shared or internet-reachable network.

Good hunting.
