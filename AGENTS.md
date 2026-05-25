# Tipinuss — Code Rules

> Mandatory for AI agents and human developers.
> Applies to: Laravel 12+, Livewire 4+, PHP 8.4+, Flux UI, Alpine.js, Tailwind CSS.

---

## Agent Constraints

### Decision Authority
- MUST ask when: requirements ambiguous, multiple valid approaches, scope unclear, business rules missing.
- MUST NOT assume intent, guess behavior, or invent features.
- MUST present ≥2 options with trade-offs for architectural decisions.

### Git
- NEVER commit unless explicitly asked. NEVER push. NEVER create branches. NEVER rewrite history.

### Architectural Boundaries
- MUST NOT install packages, restructure DB, or introduce new patterns without asking.
- Present ≥2 options with trade-offs.

### Code Generation
- MUST match existing project patterns (search repo first).
- MUST deliver complete solutions — no stubs, placeholders, `// TODO`.
- MUST verify: syntax check (`php -l`), route list, `composer lint:blade`, tests.
- MUST use `composer test:ai` to run tests (token-optimized output).
- MUST NOT add speculative code (YAGNI) or refactor unrelated code.

---

## Project Context

- **Stack:** Laravel 12, Livewire 4 (SFC), Flux UI (free), Fortify, Tailwind CSS v4, PHP 8.4
- **Pattern:** Request → DTO → Action → Service/Repository → Event → Response
- **Auth:** Laravel Fortify (views enabled), 2FA available
- **Database:** MySQL, UUID keys on primary models
- **Queue:** Database driver (sync in dev)

## Architecture Flow

```
User Input (Livewire / HTTP Request)
         ↓
Validation (FormRequest / #[Validate])
         ↓
DTO::fromRequest()
         ↓
Action::execute(DTO)
    ├─ Repository::find() / save() / delete()
    ├─ Service::validate() / calculate()
    └─ DB::transaction() for multi-model writes
         ↓
Event::dispatch()
         ↓
Response (Redirect / Flash / Toast)
```

## Directory Structure

```
app/
├── Actions/{Domain}/
├── Services/{Domain}/
├── Repositories/Contracts/ + Eloquent/
├── DTOs/{Domain}/
├── ValueObjects/
├── Models/
├── Enums/
├── Policies/
├── Events/ + Listeners/
├── Observers/
├── Exceptions/
├── Http/Controllers/ + Requests/
└── Livewire/Page/ + {Feature}/
```

## Build & Test Commands

```bash
composer test              # phpunit
composer test:ai           # phpunit (token-optimized for AI agents: compact, no ANSI, no TTY)
composer coverage          # phpunit with code coverage
composer lint              # pint + phpstan + bladestan
./vendor/bin/phpstan analyse
./vendor/bin/pint --test
```

## Skills (loaded on-demand by task)

| Skill | When to use |
|---|---|
| `php-coding` | PHP class design, typing, naming, zero-comment |
| `database` | Migrations, eager loading, transactions |
| `error-handling` | Domain exceptions, catch patterns |
| `events-listeners` | Events, listeners, ShouldQueue |
| `testing` | Test structure, factories, assertions |
| `quality-gates` | Pre-commit checks, CI commands |
| `file-templates` | New Action/Service/DTO/Livewire files |
| `laravel-best-practices` | Laravel architecture, Eloquent, caching, security, routing |
| `livewire-development` | Livewire components, wire:directives, v4 features |
| `fluxui-development` | Flux UI components, free edition |
| `tailwindcss-development` | Tailwind v4 CSS, responsive design |
| `fortify-development` | Auth, 2FA, passkeys, registration |
| `blaze-optimize` | Blade compilation optimization |

## graphify

Knowledge graph at `graphify-out/`. For codebase questions, run `graphify query "<question>"`. Use `graphify path "<A>" "<B>"` for relationships, `graphify explain "<concept>"` for focused concepts. After code changes, run `graphify update .`.

---

## SUMMARY

1. **Structure** — Domain-grouped dirs. Each layer: one responsibility.
2. **Never commit** unless explicitly told.
3. **Ask, don't guess** when requirements are unclear.
4. **Match existing patterns** — search repo first.
5. **Verify** with lint, stan, tests before finishing.
6. **See `.agents/skills/`** for detailed rules per area.
