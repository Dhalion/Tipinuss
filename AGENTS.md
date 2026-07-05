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

## Always-On Code Quality Rules

These rules are always active. Derived from **Clean Code** by Robert C. Martin.

### Primary bias to correct

Working code is not automatically clean code.

### Decision rules

- Preserve behavior, write for the next reader, and leave touched code cleaner within scope.
- Write for local reasoning and use precise names with one term per concept.
- Split boolean flags, mixed abstraction levels, and hidden side effects out of functions.
- Separate commands from queries and keep parameters small and meaningful.
- Keep the happy path readable; make invalid states, errors, and cleanup explicit instead of implicit.
- Use comments only for rationale or contracts, not to explain confusing code.
- When touching code, remove the smell most likely to make the next change risky or unclear.

### Trigger rules

- When a function both mutates and answers, split it.
- When a comment explains the flow, simplify the code first.
- When async, concurrency, or framework quirks spread the change, reduce shared mutable state and add the right boundary instead of more branching.

### Final checklist

- Local reasoning preserved?
- Clear names?
- Clear mutation boundaries?
- One smell removed?

## Book-Derived Rules (loaded on-demand)

For detailed rules from classic software engineering books, read the relevant file from `.agents/rules/` when the task matches:

| Book | File | Best for |
|---|---|---|
| Clean Code | `.agents/rules/clean-code/clean-code.mini.md` | Everyday implementation, code review, naming |
| Code Complete | `.agents/rules/code-complete/code-complete.mini.md` | Construction discipline, defect prevention, defensive coding |
| The Pragmatic Programmer | `.agents/rules/the-pragmatic-programmer/the-pragmatic-programmer.mini.md` | Engineering ethos, DRY, orthogonality, automation |
| A Philosophy of Software Design | `.agents/rules/a-philosophy-of-software-design/a-philosophy-of-software-design.mini.md` | Module design, API design, complexity reduction |
| Refactoring | `.agents/rules/refactoring/refactoring.mini.md` | Behavior-preserving code improvement, smell detection |

## Skills (loaded on-demand by task)

| Skill | When to use |
|---|---|---|
| `php-coding` | PHP class design, typing, naming, zero-comment |
| `database` | Migrations, eager loading, transactions |
| `error-handling` | Domain exceptions, catch patterns |
| `events-listeners` | Events, listeners, ShouldQueue |
| `write-tests` | Production-grade test writing: Action/Livewire/Policy patterns, mocking, data providers, edge case coverage, checklist |
| `quality-gates` | Pre-commit checks, CI commands |
| `file-templates` | New Action/Service/DTO/Livewire files |
| `laravel-best-practices` | Laravel architecture, Eloquent, caching, security, routing |
| `livewire-development` | Livewire components, wire:directives, v4 features |
| `fluxui-development` | Flux UI components, free edition |
| `tailwindcss-development` | Tailwind v4 CSS, responsive design |
| `fortify-development` | Auth, 2FA, passkeys, registration |
| `blaze-optimize` | Blade compilation optimization |
| `spec-driven-workflow` | SDD orchestration: feature-specification → architecture-planning → code-implementation |
| `feature-specification` | Complete feature specs with RFC 2119 reqs, Given/When/Then AC, edge cases, data models |
| `architecture-planning` | Senior-level architecture with quality gates, options/pros/cons, critical review checkpoint |
| `code-implementation` | Task-based implementation from approved plan, self-verify against spec, quality gates |
| `handoff` | Session handoff: compact conversation into a handoff document for another agent |
| `grill-me` | Plan/design stress-test: interview user until shared understanding reached |
| `grill-with-docs` | Plan stress-test with domain glossary: sharpens terminology, updates CONTEXT.md, creates ADRs |
| `grill-code` | Code review against ALL quality rules. Grumpy senior engineer flags violations with exact fixes. File, directory, or git diff scope. |
| `diagnose` | Debugging loop: reproduce → hypothesise → instrument → fix → regression-test |
| `improve-codebase-architecture` | Architecture deepening: find shallow modules, propose refactors, produce HTML review report |

## graphify

Knowledge graph at `graphify-out/`. For codebase questions, run `graphify query "<question>"`. Use `graphify path "<A>" "<B>"` for relationships, `graphify explain "<concept>"` for focused concepts. After code changes, run `graphify update .`.

---

## SUMMARY

1. **Structure** — Domain-grouped dirs. Each layer: one responsibility.
2. **Never commit** unless explicitly told.
3. **Ask, don't guess** when requirements are unclear.
4. **Match existing patterns** — search repo first.
5. **Verify** with lint, stan, tests before finishing.
6. **See `.agents/rules/`** for book-derived quality rules (load on-demand).
7. **See `.agents/skills/`** for detailed rules per area (standalone + optional skills). Use `write-tests` for all test creation.
8. **Use `grill-code`** to vet code against every quality rule before considering a PR or feature complete.
