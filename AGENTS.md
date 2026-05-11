# Tipinuss — Code Rules

> Mandatory for AI agents and human developers. Read BEFORE generating code.
> This is the SOLE rulebook. Code violating these rules WILL be rejected.
> Applies to: Laravel 12+, Livewire 4+, PHP 8.4+, Flux UI, Alpine.js, Tailwind CSS.

---

## 1. AGENT CONSTRAINTS

### 1.1 Decision Authority
- MUST ask the human when: requirements are ambiguous, multiple valid approaches exist, scope boundaries are unclear, business rules are missing.
- MUST NOT assume intent. MUST NOT guess behavior. MUST NOT invent features.
- MUST present ≥2 options with trade-offs when proposing architectural decisions. MUST NOT decide unilaterally.

**Examples of "ask, don't guess":**
- Unclear scope boundaries ("should this feature also support X?")
- Multiple valid implementation approaches
- Missing business rules ("what happens when...?")
- Unclear UX decisions

### 1.2 Git
- NEVER commit unless explicitly asked. NEVER push. NEVER create branches. NEVER rewrite history.
- "Explicitly asked" means the human says "commit" or "create a commit". Do NOT commit as part of a refactor, fix, or feature implementation unless told to.
- Agent prepares code for review. Human handles all git operations.

### 1.3 Architectural Boundaries
The agent is NOT the architect. MUST NOT autonomously:
- Install new packages or propose them without alternatives
- Restructure the database
- Introduce new patterns that diverge from existing style
- Make architectural decisions

**Instead:** Present ≥2 options with trade-offs and let the human decide.

### 1.4 Code Generation Standards
- MUST follow every rule in this document without exception.
- MUST match existing project patterns and conventions — search repo first.
- MUST deliver complete solutions — no stubs, no placeholders, no `// TODO`.
- MUST verify changes: syntax check (`php -l`), route list, test run.
- MUST NOT add speculative code (YAGNI). MUST NOT refactor unrelated code without instruction.
- MUST NOT write explanatory comments in generated code.
- MUST NOT mix multiple concerns in a single change.

### 1.5 Pre-Generation Checklist
Before writing code, verify:
1. Requirement fully understood? → If no: ask.
2. Existing patterns in project? → Search repo first, then match.
3. Change touches multiple concerns? → Split into separate changes.
4. Change verifiable? → Plan syntax check / test run.
5. Change minimal AND complete? → No over-engineering, no half-measures.

---

## 2. CORE PRINCIPLES

### 2.1 Maxims
- Code is read 10× more than written. **Optimize for readability.**
- Every abstraction layer has exactly ONE responsibility. No exceptions.
- Explicit is ALWAYS better than implicit. Types, returns, errors — declare everything.
- No "vibe-coding". No trial-and-error. Every line is reviewed and maintained.

### 2.2 DRY, KISS, YAGNI
- **DRY** — MUST NOT duplicate logic. Shared logic belongs in Services or Traits.
- **KISS** — The simplest correct solution is the right solution.
- **YAGNI** — MUST NOT implement anything not needed RIGHT NOW. No "might need later" features.

### 2.3 SOLID
| Principle | Rule |
|---|---|
| **Single Responsibility** | One class = one reason to change. Actions perform ONE operation. |
| **Open/Closed** | Extend behavior via interfaces and DI, not by modifying existing classes. |
| **Liskov Substitution** | Every interface implementation MUST fully satisfy the interface's contract. |
| **Interface Segregation** | Small, specific interfaces. No `RepositoryInterface` with 30 methods. |
| **Dependency Inversion** | ALWAYS depend on interfaces, NEVER on concrete implementations. |

### 2.4 Modern PHP Features
MUST use these PHP 8.1+ features:

| Feature | Rule | Anti-Pattern |
|---|---|---|
| **Enums** | Backed enums instead of constants/magic strings | `const OPEN = 'open'` → `enum BetStatus: string` |
| **Match** | Instead of switch/if-elseif chains | `if ($x == 'a')... elseif...` → `match($x) { 'a' => ... }` |
| **Constructor Promotion** | All constructor params promoted with readonly | Manual property assignment |
| **Named Arguments** | When >2 params or any boolean param | `$this->createBet($user, true, false, 'title')` |
| **Null-Safe `?->`** | ONLY when null is expected, not to hide bugs | `$user?->admin()` when admin is always expected |
| **`readonly class`** | For all DTOs and Value Objects | Mutable DTOs |
| **`final`** | On all classes not designed for inheritance | Extendable utility classes |

---

## 3. PHP FUNDAMENTALS

### 3.1 Strict Typing
MUST begin every PHP file with `declare(strict_types=1);`:
```php
<?php
declare(strict_types=1);
namespace App\Actions\Betting;
```

MUST type-declare:
- All parameters
- All return types
- All properties
- All constructor params

Nullable: use `?Type` — never leave type undeclared.

Generics: use `@return Collection<int, Model>` PHPDoc:
```php
/** @return Collection<int, UserBet> */
public function getPlacedBets(): Collection
```

MUST pass PHPStan level 5+.

### 3.2 Class Design
- MUST mark all classes `final` unless explicitly designed for inheritance.
- MUST use `readonly class` for DTOs and Value Objects.
- MUST use constructor promotion with `private`/`public readonly`.
- MUST use backed `enum` instead of constants or magic strings.
- MUST use `match` instead of `switch` or `if/elseif` chains.
- MUST use named arguments when method has >2 params or any boolean param.
- MUST use null-safe operator `?->` ONLY when null is an expected state.

### 3.3 Zero-Comment Policy
Code that needs a comment needs refactoring. Self-document through naming.

**ALLOWED:**
- PHPDoc `@return`/`@param` for generics: `@return Collection<int, UserBet>`
- "Why" comments for non-obvious business rules or workarounds

**FORBIDDEN:**
- "What" comments: `// validate user input`
- "How" comments: `// loop through all bets`
- Commented-out code
- TODO/FIXME/HACK in production code

**PATTERN:**
```php
$potentialWinnings = $odds * $amountWagered;
```

**ANTI-PATTERN:**
```php
$r = $a * $b; // calculate winnings
```

### 3.4 Naming Conventions

**Classes:**

| Type | Pattern | Example |
|---|---|---|
| Action | `{Verb}{Noun}Action` | `PlaceBetAction` |
| Service | `{Noun}Service` | `BetCalculationService` |
| Repo Interface | `{Noun}RepositoryInterface` | `BetRepositoryInterface` |
| Repo Impl | `Eloquent{Noun}Repository` | `EloquentBetRepository` |
| DTO | `{Noun}Data` | `PlaceBetData` |
| Value Object | Noun, no suffix | `Soapnuts`, `Odds` |
| Policy | `{Noun}Policy` | `BetPolicy` |
| Observer | `{Noun}Observer` | `BetObserver` |
| Model | Singular noun | `User`, `Bet`, `BetOption` |
| Enum | Singular noun | `BetStatus`, `UserBetStatus` |
| Event | Past tense | `BetPlaced`, `BetClosed` |
| Listener | `{Action}When{Event}` | `PayWinnersWhenBetClosed` |
| Exception | `{Noun}Exception` | `InsufficientBalanceException` |
| FormRequest | `{Verb}{Noun}Request` | `StoreBetRequest` |

**Methods:** Verb-first, clear intent. Query methods: `is`/`has`/`can`/`get` prefix. Action methods: imperative.

| PATTERN | ANTI-PATTERN |
|---|---|
| `calculateWinnings()` | `winningsCalc()` |
| `isOpen()` | `open` (property) |
| `validateBalance()` | `checkIfUserHasEnoughBalance()` |
| `canCloseBet()` | `canUserCloseBet()` |

**Variables:** Semantic, full words, NO abbreviations.

| PATTERN | ANTI-PATTERN |
|---|---|
| `$potentialWinnings` | `$pw` |
| `$amountWagered` | `$amt` |
| `$userBets` (collection) | `$bets` |
| `$isValid`, `$hasBalance` | `$valid`, `$balance` |

**Directories:** Singular for classes/models (`User`), plural for folders (`Actions/`, `Models/`). Domain-grouped: `Actions/Betting/`, `Services/User/`.

---

## 4. LARAVEL ARCHITECTURE

### 4.1 Layer Separation — STRICT

```
Request → Validation → DTO → Action → Service/Repository → Event → Response
```

Each layer has ONE responsibility. Violations are rejected.

### 4.2 Thin Controllers
MUST ONLY: (1) accept request, (2) delegate to Action/Service, (3) return response.
MUST use FormRequest for validation. MUST NOT contain business logic.

**PATTERN:**
```php
final class BetController extends Controller {
    public function store(StoreBetRequest $request, CreateBetAction $action): RedirectResponse {
        $bet = $action->execute(CreateBetData::fromRequest($request));
        return redirect()->route('bets.show', $bet);
    }
}
```

**ANTI-PATTERN:**
```php
final class BetController extends Controller {
    public function store(Request $request): RedirectResponse {
        $validated = $request->validate([...]); // → belongs in FormRequest
        $bet = Bet::create($validated);          // → belongs in Action
        $user->decrement('soapnuts', $amount);   // → belongs in Service
        return redirect()->route('bets.show', $bet);
    }
}
```

### 4.3 Form Requests
Validation and authorization belong EXCLUSIVELY in dedicated FormRequest classes.

```php
final class StoreBetRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()->can('create', Bet::class);
    }
    public function rules(): array {
        return ['title' => ['required', 'string', 'max:255']];
    }
}
```

### 4.4 Action Classes
Single business operation per class. `final class`. ONE public method: `execute()`. Dependencies via constructor injection. Returns result or throws domain exception. Wrap multi-model writes in `DB::transaction()`.

```php
final class PlaceBetAction {
    public function __construct(
        private BettingValidationService $validation,
        private BetCalculationService $calculation,
        private UserBalanceService $balance,
        private UserBetRepositoryInterface $userBets,
    ) {}

    public function execute(PlaceBetData $data): UserBet {
        $this->validation->validateBalanceSufficient($data->user, $data->amount);
        $potentialWinnings = $this->calculation->calculatePotentialWinnings($data->option, $data->amount);

        return DB::transaction(function () use ($data, $potentialWinnings): UserBet {
            $this->balance->decrementBalance($data->user, $data->amount);
            return $this->userBets->save(new UserBet(['user_id' => $data->user->id, 'bet_option_id' => $data->option->id, 'amount_wagered' => $data->amount, 'potential_winnings' => $potentialWinnings]));
        });
    }
}
```

### 4.5 Services
Reusable domain logic called by multiple Actions. `final class`. Stateless — no internal state between calls. DI for all dependencies. MUST NOT create models directly (that's the Action's job).

```php
final class BetCalculationService {
    public function calculatePotentialWinnings(BetOption $option, int $amountWagered): int {
        return (int) ($option->odds * $amountWagered);
    }
}
```

### 4.6 Models — Thin
**Models MUST contain:** relationships (`belongsTo`, `hasMany`), casts (`protected function casts(): array`), scopes (`scopeOpen`, `scopeForUser`), accessors/mutators, simple state checks (`isOpen(): bool`).

**Models MUST NOT contain:** complex business logic, cross-model calculations, direct side effects (emails, event dispatching).

```php
final class Bet extends Model {
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['title', 'status', 'creator_id'];

    protected function casts(): array {
        return ['status' => BetStatus::class];
    }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function betOptions(): HasMany { return $this->hasMany(BetOption::class); }
    public function isOpen(): bool { return $this->status === BetStatus::Open; }
    public function scopeOpen(Builder $query): Builder { return $query->where('status', BetStatus::Open); }
}
```

### 4.7 DTOs (Data Transfer Objects)
Typed, immutable data carriers at layer boundaries. `final readonly class`. Constructor with promoted `public` properties. Factory methods: `fromRequest()`, `fromArray()`, `make()`. NO business logic. NO methods beyond factory constructors. Replaces EVERY `array $data` at method/layer boundaries.

```php
final readonly class PlaceBetData {
    public function __construct(
        public User $user,
        public BetOption $option,
        public int $amount,
    ) {}
    public static function make(User $user, BetOption $option, int $amount): self {
        return new self(user: $user, option: $option, amount: $amount);
    }
}
```

### 4.8 Value Objects
Domain primitives with invariants (currency, odds, email). Immutable — methods return new instances. Self-validating — constructor throws on invalid input. Equality by value — implement `equals()`. No Eloquent, no entity ID.

```php
final class Soapnuts {
    public readonly int $value;
    public function __construct(int $value) {
        if ($value < 0) throw new InvalidArgumentException("Cannot be negative: {$value}");
        $this->value = $value;
    }
    public function add(Soapnuts $other): self { return new self($this->value + $other->value); }
    public function isGreaterThanOrEqual(Soapnuts $other): bool { return $this->value >= $other->value; }
    public function equals(Soapnuts $other): bool { return $this->value === $other->value; }
}
```

### 4.9 Repository Pattern
- MUST depend on interface, NEVER on Eloquent implementation.
- Eager loading (`->with([...])`) belongs in Repository, NOT in Livewire or Actions.
- Repositories are pure query/persistence — no business decisions.
- Bind interface → implementation in `AppServiceProvider::register()`.

```php
// Interface
interface BetRepositoryInterface {
    public function findByIdOrFail(string $id): Bet;
    public function save(Bet $bet): Bet;
    public function delete(Bet $bet): void;
}

// Implementation
final class EloquentBetRepository implements BetRepositoryInterface {
    public function findByIdOrFail(string $id): Bet { return Bet::with('betOptions')->findOrFail($id); }
    public function save(Bet $bet): Bet { $bet->save(); return $bet; }
    public function delete(Bet $bet): void { $bet->delete(); }
}
```

**Rules:**
- ✅ Always inject the interface, never `new EloquentBetRepository()` or `Bet::query()` in Livewire/Actions
- ✅ Eager-load in the repository — `->with([...])` lives here, not in callers
- ✅ Single responsibility — repositories are query/persistence only
- ✅ Swap for tests — bind a fake in `TestCase::setUp()` for isolation
- ❌ No filtering/sorting passed as raw strings — use typed parameters
- ❌ No business decisions inside a repository — no `if ($isBanned)` here

### 4.10 Authorization & Policies
- EVERY authorization-required action MUST go through a Policy.
- `$this->authorize()` in Livewire components. `Gate::authorize()` in Actions.
- Policies MUST check ownership AND state.

```php
final class BetPolicy {
    public function closeBet(User $user, Bet $bet): bool {
        return ($user->id === $bet->user_id || $user->isAdmin()) && $bet->isOpen();
    }
    public function deleteBet(User $user, Bet $bet): bool {
        return ($user->id === $bet->user_id || $user->isAdmin()) && $bet->isOpen();
    }
}
```

### 4.11 Observers
Model lifecycle side effects only (logging, cache invalidation, notifications). MUST NOT contain business logic. Register in `AppServiceProvider::boot()`.

```php
final class BetObserver {
    public function created(Bet $bet): void {
        cache()->forget("bets.open.count");
    }
}
```

### 4.12 Directory Structure
```
app/
├── Actions/{Domain}/      # One operation per class
├── Services/{Domain}/     # Reusable logic
├── Repositories/
│   ├── Contracts/         # Interfaces
│   └── Eloquent/          # Implementations
├── DTOs/{Domain}/         # Typed immutable data carriers
├── ValueObjects/          # Domain primitives
├── Models/                # Eloquent (relationships, casts, scopes only)
├── Enums/                 # Backed enums
├── Policies/              # Authorization
├── Events/                # Domain events
├── Listeners/             # Event handlers
├── Observers/             # Model lifecycle hooks
├── Exceptions/            # Domain exceptions
├── Http/Controllers/      # Thin routing
├── Http/Requests/         # FormRequest validation
├── Livewire/Page/         # Page components
├── Livewire/{Feature}/    # Feature components
└── Providers/             # Bindings
```

### 4.13 Request → Response Flow
```
User Input (Livewire / HTTP Request)
         ↓
Validation (FormRequest / #[Validate])
         ↓
DTO::fromRequest() (Typed data carrier)
         ↓
Action::execute(DTO) (Business Logic)
    ├─ Repository::find() (Data Access)
    ├─ Service::validate() (Domain Rules)
    ├─ Service::calculate() (Calculations)
    └─ Repository::save() (Persistence)
         ↓
Event::dispatch() (Side effects)
         ↓
Response (Redirect / Flash / Event Dispatch)
```

---

## 5. LIVEWIRE RULES

### 5.1 Thin Components
Components are UI binding only. They delegate to Actions/Services.

**ALLOWED:** Accept user input, validate with `#[Validate]`, delegate to Actions, dispatch events, pass render data to view.

**FORBIDDEN:** Business logic (calculations, rules, decisions), direct DB queries (`Model::query()`), complex state transformations.

**PATTERN:**
```php
final class BetDetail extends Component {
    #[Locked] public string $betId;
    public function closeBet(string $winningOptionId, CloseBetAction $action): void {
        $this->authorize('closeBet', $bet = $this->bet());
        $action->execute(CloseBetData::make(bet: $bet, winningOptionId: $winningOptionId));
        $this->dispatch('bet-closed');
    }
}
```

**ANTI-PATTERN:**
```php
final class BetDetail extends Component {
    public function closeBet(string $winningOptionId): void {
        $winningOption = BetOption::findOrFail($winningOptionId); // ← direct DB query
        foreach ($winningOption->userBets as $userBet) {         // ← business logic
            $userBet->user->increment('soapnuts', ...);
        }
    }
}
```

### 5.2 State Management
- MUST keep `public` properties minimal — only data needed for frontend reactivity.
- MUST use `#[Locked]` for client-immutable properties (IDs, roles).
- MUST use `#[Computed]` for derived values (cached per request cycle).
- MUST use `#[Url]` for URL-reflected state.
- MUST NOT pass large Eloquent models as `public` properties (payload size + security risk).
- MUST use `#[Validate]` attributes on input properties.
- MUST use `#[On('event-name')]` for event listeners.

### 5.3 Livewire Form Objects
MUST use Form Objects for forms with >3 fields.

```php
final class CreateBetForm extends Form {
    #[Validate('required|string|max:255')]
    public string $title = '';
    #[Validate('required|string|max:1000')]
    public string $description = '';
    /** @var array<int, array{title: string, odds: float}> */
    #[Validate('required|array|min:2')]
    public array $options = [];
}
```

### 5.4 Events & DOM Updates
- `$this->dispatch('event-name')` for cross-component communication.
- `#[On('event-name')]` as listener declaration.
- `wire:navigate.hover` for SPA-like navigation.
- `wire:poll` ONLY when absolutely necessary — interval ≥5s.
- Alpine.js for pure client-side interactions (modals, toggles, animations).
- Livewire for everything that needs server state.

### 5.5 Performance
- Default: `wire:model` (deferred). `wire:model.live` ONLY when real-time feedback required.
- If live binding needed: `wire:model.live.debounce.300ms`.
- Use pagination — never load full collections.
- `#[Computed]` is cached per request cycle — use instead of repeated queries.

---

## 6. DATABASE

### 6.1 Eager Loading
MUST call in `AppServiceProvider::boot()`:
```php
Model::preventLazyLoading(!app()->isProduction());
Model::shouldBeStrict(!app()->isProduction());
```

N+1 queries are bugs. MUST eager-load in Repository, NEVER rely on lazy loading.

**PATTERN — eager loading in repository:**
```php
public function paginateOpen(int $perPage = 20): LengthAwarePaginator {
    return Bet::where('status', BetStatus::Open)
        ->with(['creator', 'betOptions', 'betOptions.userBets'])
        ->latest()->paginate($perPage);
}
```

**ANTI-PATTERN — lazy loading in loop:**
```blade
@foreach($bets as $bet)
    {{ $bet->creator->name }}  {{-- N+1 if not eager-loaded --}}
@endforeach
```

### 6.2 Transactions
- MUST wrap every multi-model write in `DB::transaction()`.
- Rule: one Action = one transaction.

```php
return DB::transaction(function () use ($data): UserBet {
    $this->balance->decrementBalance($data->user, $data->amount);
    return $this->userBets->save(new UserBet([...]));
});
```

### 6.3 Migrations
- MUST implement `down()` method (rollback capability).
- MUST specify explicit `onDelete` strategy on foreign keys.
- MUST NOT add `nullable()` without compelling domain reason.
- MUST add indexes on frequently filtered/sorted columns.

---

## 7. ERROR HANDLING

- MUST create domain-specific exceptions (`BetException`, `InsufficientBalanceException`).
- MUST use static factory methods on exceptions for descriptive construction.
- MUST throw exceptions in Actions/Services for domain errors.
- MUST catch specifically in Livewire — targeted catch for user feedback.
- MUST NOT use catch-all `catch (\Exception $e)`. Let the global handler work.
- MUST NOT silently swallow errors with `logger()` in catch blocks.

**PATTERN:**
```php
// Domain exception
final class InsufficientBalanceException extends BetException {
    public static function forUser(User $user, int $required): self {
        return new self("Insufficient balance: has {$user->soapnuts}, needs {$required}");
    }
}

// In Service — throw
public function validateBalance(User $user, int $amount): void {
    if ($user->soapnuts < $amount) {
        throw InsufficientBalanceException::forUser($user, $amount);
    }
}

// In Livewire — targeted catch
public function placeBet(PlaceBetAction $action): void {
    try {
        $action->execute(...);
        Flux::toast(__('bets.placed_successfully'));
    } catch (InsufficientBalanceException) {
        $this->addError('amount', __('bets.insufficient_balance'));
    } catch (BetException $e) {
        Flux::toast($e->getMessage(), variant: 'danger');
    }
}
```

**ANTI-PATTERN — catch-all:**
```php
try { $action->execute(...); }
catch (\Exception $e) { logger()->error($e->getMessage()); } // silently swallowed
```

---

## 8. EVENTS & LISTENERS

| Use events for | Use direct calls for |
|---|---|
| Notifications | Balance calculations |
| Logging / audit trail | Validation |
| Cache invalidation | Core calculations |
| Cross-domain side effects | Persistence |

**Rules:**
- Event names: past tense (`BetPlaced`, `BetClosed`).
- Events carry model instances only — no primitive arrays.
- Listener names: `{Action}When{Event}` → `NotifyCreatorWhenBetPlaced`.
- MUST use `ShouldQueue` on listeners that do I/O (email, external API).
- MUST NOT dispatch events from listeners (creates invisible chains).
- MUST NOT put business logic in listeners — delegate to Actions.

```php
final class BetPlaced {
    use Dispatchable;
    public function __construct(public readonly Bet $bet) {}
}

final class NotifyCreatorWhenBetPlaced implements ShouldQueue {
    public function handle(BetPlaced $event): void {
        // Notification logic, NOT business logic
    }
}
```

---

## 9. FRONTEND & BLADE

- MUST use Flux UI components as primary building blocks.
- MUST use Tailwind CSS. MUST NOT use inline styles except for dynamic values.
- MUST use Alpine.js for client-side interactions.
- MUST design mobile-first responsive. MUST test on mobile viewports.
- MUST NOT put business logic in Blade — display logic only (`@if`, `@foreach`, formatting).
- MUST prepare complex calculations in `#[Computed]` properties or Livewire component.
- MUST use `@include` for reusable template fragments. Livewire components for reactive elements.
- MUST use `__()` / translation files for ALL user-facing strings. No hardcoded strings.
- Translation files structured by feature: `lang/{locale}/bets.php`, `lang/{locale}/account.php`.

---

## 10. TESTING

- MUST test every Action: ≥1 happy-path test + ≥1 error test.
- MUST test Livewire components with `Livewire::test()`.
- MUST use model factories — no hardcoded test data.
- MUST keep tests independent (no shared state between tests).
- Repository fakes for unit tests, real DB for feature tests.

**Test Structure:**
```
tests/
├── Feature/{Domain}/     # Integration (DB, HTTP, Livewire)
├── Unit/{Layer}/         # Isolated (Services, Actions, Value Objects)
└── TestCase.php
```

**Naming:**
- Feature: `test{Feature}{Scenario}` → `testPlaceBetDeductsBalance()`
- Unit: `test{Methode}{Scenario}` → `testCalculateWinningsReturnsCorrectAmount()`
- Or: `#[Test]` attribute with descriptive method name

**Example:**
```php
public function testPlaceBetDeductsBalance(): void {
    $user = User::factory()->create(['soapnuts' => 1000]);
    $option = BetOption::factory()->create(['odds' => 2.0]);
    $userBet = (new PlaceBetAction(...))->execute($user, $option, 100);
    $this->assertEquals(900, $user->fresh()->soapnuts);
    $this->assertEquals(100, $userBet->amount_wagered);
}
```

---

## 11. QUALITY GATES

All MUST pass before code is considered complete:
```bash
./vendor/bin/phpstan analyse          # Static analysis
./vendor/bin/pint --test              # Code style
php artisan test                      # Test suite
```

### Pre-Merge Checklist
- [ ] `declare(strict_types=1)` in every PHP file
- [ ] All methods have return type declarations
- [ ] All parameters are type-hinted
- [ ] All properties have access modifiers and types
- [ ] No abbreviations in variable/method names
- [ ] Classes follow naming conventions (§3.4)
- [ ] Multi-model writes in `DB::transaction()`
- [ ] Relationships eager-loaded in Repository, not in Livewire/Actions
- [ ] No business logic in Livewire components or Listeners
- [ ] No direct DB queries in Livewire
- [ ] DTOs at layer boundaries instead of raw arrays
- [ ] Repository interface injected, never concrete Eloquent class
- [ ] `#[Validate]` on Livewire input properties
- [ ] `#[On]` for Livewire event listeners
- [ ] Domain exceptions thrown (no catch-all, no silent failures)
- [ ] No comments (code is self-documenting)
- [ ] PHPStan passes
- [ ] Pint passes
- [ ] Tests pass

---

## 12. FILE TEMPLATES

### Action
```php
<?php declare(strict_types=1);
namespace App\Actions\{Domain};
final class {Verb}{Noun}Action {
    public function __construct(private {Service} $service) {}
    public function execute({DTO|Model} $data): {ReturnType} {}
}
```

### Service
```php
<?php declare(strict_types=1);
namespace App\Services\{Domain};
final class {Noun}Service {
    public function {verb}({params}): {ReturnType} {}
}
```

### DTO
```php
<?php declare(strict_types=1);
namespace App\DTOs\{Domain};
final readonly class {Noun}Data {
    public function __construct(public {Type} $property) {}
    public static function make(): self {}
}
```

### Repository Interface
```php
<?php declare(strict_types=1);
namespace App\Repositories\Contracts;
interface {Noun}RepositoryInterface {
    public function findByIdOrFail(string $id): {Model};
    public function save({Model} $model): {Model};
    public function delete({Model} $model): void;
}
```

### Livewire Component
```php
<?php declare(strict_types=1);
namespace App\Livewire\{Feature};
use Livewire\Attributes\{Computed, Locked, On, Validate};
use Livewire\Component;
final class {Name} extends Component {
    #[Locked] public string $id;
    public function render(): \Illuminate\View\View {
        return view('livewire.{feature}.{name}');
    }
}
```

### Domain Exception
```php
<?php declare(strict_types=1);
namespace App\Exceptions;
final class {Noun}Exception extends \RuntimeException {
    public static function {reason}({params}): self {
        return new self("Descriptive message with {$context}");
    }
}
```

### Observer
```php
<?php declare(strict_types=1);
namespace App\Observers;
final class {Noun}Observer {
    public function created({Model} $model): void {}
}
```

---

## SUMMARY

1. **Structure** — Domain-grouped directories. Each layer: exactly one responsibility.
2. **Types** — `strict_types=1` everywhere. No untyped params, properties, or returns.
3. **Separation** — Actions (operations) → Services (logic) → Repositories (data) → DTOs (carriers) → Livewire (UI only).
4. **No Comments** — Self-documenting code through naming.
5. **Errors** — Domain exceptions. No catch-all. Explicit handling.
6. **Database** — Eager loading enforced. Transactions for multi-model writes. `shouldBeStrict()` in dev.
7. **Testing** — Actions, Services, Value Objects, Livewire: tested. No excuses.
8. **Agent** — Asks when unclear. Does not make architecture decisions. Never commits autonomously.

**This standard is non-negotiable.**
