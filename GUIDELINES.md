# Tipinuss Code Quality Guidelines

Quality-driven code is a community responsibility. These guidelines define how we write code that is human-first, maintainable, and built for collaboration.

---

## 1. Structure & Organization

### Domain-Driven Directory Layout

```
app/
├── Actions/           # Business operations (single purpose)
│   └── Betting/       # Domain-specific actions
├── Services/          # Reusable business logic (multiple operations)
│   ├── Betting/
│   └── User/
├── Repositories/      # Data access abstraction (interface + Eloquent impl)
│   ├── Contracts/     # Interfaces: BetRepositoryInterface
│   └── Eloquent/      # Implementations: EloquentBetRepository
├── DTOs/              # Data Transfer Objects (typed, immutable data carriers)
│   └── Betting/
├── ValueObjects/      # Domain value types (Money, Odds, Email)
├── Policies/          # Authorization rules
├── Models/            # Data layer with relationships
├── Enums/             # Strongly typed constants
├── Http/
│   └── Controllers/   # Minimal, delegate to Actions
├── Livewire/          # Presentation layer (no business logic)
│   ├── Page/          # Page-level components
│   └── Bets/          # Feature-specific components
├── Providers/         # Service container registration + repository bindings
├── Observers/         # Model lifecycle hooks
└── Events/            # Domain events + Listeners
```

### Folder Naming
- **Singular** for classes/models: `User`, `Bet`, `BetOption`
- **Plural** for collections: `Actions/`, `Services/`, `Policies/`, `Models/`
- **Domain grouping**: `Actions/Betting/`, `Services/Betting/`, `Services/User/`

---

## 2. Naming Conventions

### Classes

| Type | Pattern | Example |
|------|---------|---------|
| Action | `{Noun}{Verb}Action` | `PlaceBetAction`, `CloseBetAction` |
| Service | `{Noun}Service` | `BetCalculationService` |
| Repository Interface | `{Noun}RepositoryInterface` | `BetRepositoryInterface` |
| Repository Impl | `Eloquent{Noun}Repository` | `EloquentBetRepository` |
| DTO | `{Noun}Data` | `PlaceBetData`, `CreateBetData` |
| Value Object | Noun, no suffix | `Soapnuts`, `Odds`, `EmailAddress` |
| Policy | `{Noun}Policy` | `BetPolicy` |
| Observer | `{Noun}Observer` | `BetObserver` |
| Model | Singular noun | `User`, `Bet` |
| Enum | Singular noun | `BetStatus`, `UserBetStatus` |
| Event | Past-tense noun phrase | `BetPlaced`, `BetClosed` |
| Listener | `{Action}When{Event}` | `PayOutWinnersWhenBetClosed` |
| Livewire Component | `PascalCase` | `PlaceBetModal`, `BetListing` |

### Methods

- **Verb-first**, clear intent
- **Present tense** or **imperative**: `create()`, `validate()`, `calculateWinnings()`
- **Query methods** start with `get`, `has`, `is`, `can`: `getBetStatus()`, `isOpen()`, `canClose()`
- **Action methods** are imperative: `placeBet()`, `closeBet()`, `deleteOption()`

**Examples:**
```php
// Good
$service->calculateWinnings($odds, $amount);
$validation->validateBalance($user, $amount);
$bet->isOpen();
$policy->canCloseBet($user, $bet);

// Bad
$service->winningsCalc($odds, $amount);
$validation->checkIfUserHasEnoughBalance($user, $amount);
$bet->open;
$policy->canUserCloseBet($user, $bet);
```

### Variables & Properties

- **Semantic, descriptive names** – NO abbreviations
- **Full words**: `amount_wagered` not `amt`, `created_at` not `creat_dt`
- **Plural for collections**: `$userBets`, `$placedBets`
- **Boolean-like prefixes for queries**: `$isValid`, `$hasBalance`, `$canPlace`

**Examples:**
```php
// Good
$potentialWinnings = $odds * $amount;
$userBalance = $user->soapnuts;
$placedBets = $bet->userBets()->latest()->get();

// Bad
$pw = $odds * $amt;
$bal = $user->soapnuts;
$bets = $bet->userBets()->latest()->get();
```

---

## 3. Type Safety

### Type Declarations Required On

✅ **All method parameters**
```php
public function placeBet(User $user, BetOption $option, int $amount): UserBet
```

✅ **All method return types**
```php
public function isValid(): bool
public function calculate(): float
public function find(): ?Bet
```

✅ **All property declarations**
```php
private int $amount;
protected string $status;
public ?User $user = null;
```

✅ **All constructor parameters**
```php
public function __construct(
    private BettingValidationService $validation,
    private BetCalculationService $calculation,
) {}
```

### Type Strictness

Add `declare(strict_types=1);` at the top of every PHP file:

```php
<?php

declare(strict_types=1);

namespace App\Actions\Betting;
```

### Nullable Types

- Use `?Type` for nullable parameters/returns
- Initialize `null` coalescing explicitly in properties
- Validate before use (PHPStan level 5+)

**Good:**
```php
public function find(int $id): ?Bet
{
    return Bet::find($id);
}

public function execute(?string $note = null): Bet
{
    $this->bet->note = $note;
    return $this->bet;
}
```

### Collections with Generics (PHPStan Level 5+)

Use `array<Key, Type>` or Laravel's collection types:

```php
/** @return array<int, Bet> */
public function getOpenBets(): array
{
    return Bet::where('status', BetStatus::Open)->get()->all();
}

/** @return Collection<int, UserBet> */
public function getPlacedBets(): Collection
{
    return $this->userBets()->get();
}
```

---

## 4. Separation of Concerns

### Layer Responsibilities

**Actions** – Single business operation
- Execute one workflow
- Coordinate Services & Models
- Throw exceptions for errors
- Return results or modified models
- Dependency inject Services

**Example: PlaceBetAction**
```php
class PlaceBetAction
{
    public function execute(User $user, BetOption $option, int $amount): UserBet
    {
        $this->validation->validateBalance($user, $amount);
        $this->validation->validateOption($option);
        
        $winnings = $this->calculation->calculateWinnings($option->odds, $amount);
        
        $user->decrementBalance($amount);
        return UserBet::create([...]);
    }
}
```

**Services** – Reusable business logic
- Encapsulate domain rules
- Called by Actions or other Services
- No direct model creation (except in Actions)
- Stateless and testable

**Example: BettingValidationService**
```php
class BettingValidationService
{
    public function validateBalance(User $user, int $amount): void
    {
        if ($user->soapnuts < $amount) {
            throw new InsufficientBalanceException(...);
        }
    }
}
```

**Livewire Components** – Presentation only
- Handle user input & events
- Validate with #[Validate] attributes
- Delegate to Actions for business logic
- Dispatch events for cross-component communication
- No database queries directly (use Services)

**Example: PlaceBetModal**
```php
class PlaceBetModal extends Component
{
    #[Validate('integer|min:1|max:100000')]
    public int $amount = 0;
    
    public function placeBet(PlaceBetAction $action): void
    {
        $userBet = $action->execute(auth()->user(), $this->option, $this->amount);
        $this->dispatch('bet-placed', betId: $userBet->id);
    }
}
```

**Models** – Data layer
- Define relationships
- Cast attributes
- Use Enums for status fields
- No business logic (methods are thin query helpers)

**Example: Bet**
```php
class Bet extends Model
{
    protected $casts = [
        'status' => BetStatus::class,
        'expires_at' => 'datetime',
    ];
    
    public function userBets(): HasMany { ... }
    public function isOpen(): bool { return $this->status === BetStatus::Open; }
}
```

### Request → Response Flow

```
User Input (Livewire)
         ↓
#[Validate] (Input Validation)
         ↓
DTO::fromRequest() (Typed data carrier)
         ↓
Action::execute(DTO) (Business Logic)
    ├─ Repository::find() (Data Access)
    ├─ Service::validate() (Domain Rules)
    ├─ Service::calculate() (Calculations)
    └─ Repository::save() / Model::create() (Persistence)
         ↓
Event::dispatch() (Side effects, listeners)
         ↓
Livewire Response (Modal close, Flash message)
```

---

## 5. Data Transfer Objects (DTOs)

DTOs are **typed, immutable data carriers** that cross layer boundaries. They replace raw arrays and make method signatures explicit and safe.

### When to Use

- Passing validated input from Livewire → Action
- Carrying query results between Services
- Any boundary where you would otherwise pass an `array`

### Implementation with PHP 8.1+ readonly

```php
<?php declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\BetOption;
use App\Models\User;

final class PlaceBetData
{
    public function __construct(
        public readonly User $user,
        public readonly BetOption $option,
        public readonly int $amount,
    ) {}

    public static function fromRequest(User $user, BetOption $option, int $amount): self
    {
        return new self(
            user: $user,
            option: $option,
            amount: $amount,
        );
    }
}
```

### In an Action

```php
// Action receives a typed DTO, not individual arguments
public function execute(PlaceBetData $data): UserBet
{
    $this->validation->validateBalance($data->user, $data->amount);
    // ...
}
```

### Rules

✅ **Always `readonly`** – DTOs are never mutated after construction  
✅ **Named constructor `fromRequest()` or `fromArray()`** – construction logic in one place  
✅ **No methods beyond factory constructors** – DTOs carry data, not behaviour  
✅ **PHPStan `@param PlaceBetData $data`** – explicit typing everywhere  
❌ **No business logic inside DTOs**  
❌ **No optional `array $data` arguments** – replace every such signature with a DTO  

---

## 6. Value Objects

A Value Object represents a **domain concept whose identity is its value**, not a database ID. Use them for domain primitives that carry invariants.

### When to Use

- Currency / balance (`Soapnuts`)
- Probabilities / odds (`Odds`)
- Contact data (`EmailAddress`)
- Any primitive that has validation rules or domain meaning

### Implementation

```php
<?php declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

final class Soapnuts
{
    public readonly int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException("Soapnuts amount cannot be negative: {$value}");
        }

        $this->value = $value;
    }

    public function add(Soapnuts $other): self
    {
        return new self($this->value + $other->value);
    }

    public function subtract(Soapnuts $other): self
    {
        return new self($this->value - $other->value);
    }

    public function isGreaterThanOrEqual(Soapnuts $other): bool
    {
        return $this->value >= $other->value;
    }

    public function equals(Soapnuts $other): bool
    {
        return $this->value === $other->value;
    }
}
```

### Rules

✅ **Immutable** – never modify state, always return new instances  
✅ **Self-validating** – constructor throws on invalid input  
✅ **Equality by value** – implement `equals()` comparison  
✅ **Rich domain methods** – `add()`, `subtract()`, `isGreaterThanOrEqual()`  
❌ **No entity identity** – Value Objects have no `id`  
❌ **No Eloquent inheritance** – Value Objects are plain PHP, not models  

---

## 7. Repository Pattern

Repositories **abstract the data access layer** behind an interface. Code depending on data never knows whether it's talking to Eloquent, a cache, or an API.

### Interface Definition

```php
<?php declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bet;
use App\Enums\BetStatus;
use Illuminate\Pagination\LengthAwarePaginator;

interface BetRepositoryInterface
{
    public function findById(string $id): ?Bet;

    public function findByIdOrFail(string $id): Bet;

    /** @return LengthAwarePaginator<Bet> */
    public function paginateOpen(int $perPage = 20): LengthAwarePaginator;

    /** @return LengthAwarePaginator<Bet> */
    public function paginateByUser(string $userId, int $perPage = 20): LengthAwarePaginator;

    public function save(Bet $bet): Bet;

    public function delete(Bet $bet): void;
}
```

### Eloquent Implementation

```php
<?php declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BetStatus;
use App\Models\Bet;
use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentBetRepository implements BetRepositoryInterface
{
    public function findById(string $id): ?Bet
    {
        return Bet::find($id);
    }

    public function findByIdOrFail(string $id): Bet
    {
        return Bet::findOrFail($id);
    }

    public function paginateOpen(int $perPage = 20): LengthAwarePaginator
    {
        return Bet::where('status', BetStatus::Open)
            ->with(['creator', 'betOptions'])
            ->latest()
            ->paginate($perPage);
    }

    public function paginateByUser(string $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Bet::where('user_id', $userId)
            ->with(['betOptions'])
            ->latest()
            ->paginate($perPage);
    }

    public function save(Bet $bet): Bet
    {
        $bet->save();
        return $bet;
    }

    public function delete(Bet $bet): void
    {
        $bet->delete();
    }
}
```

### Bind in a Service Provider

```php
// app/Providers/AppServiceProvider.php  – or a dedicated RepositoryServiceProvider

public function register(): void
{
    $this->app->bind(
        \App\Repositories\Contracts\BetRepositoryInterface::class,
        \App\Repositories\Eloquent\EloquentBetRepository::class,
    );
}
```

### Inject and Use

```php
// In a Service or Action – depend on the interface, never the implementation
class BetsListing extends Component
{
    public function __construct(private BetRepositoryInterface $bets) {}

    public function render(): View
    {
        return view('livewire.bets.listing', [
            'bets' => $this->bets->paginateOpen(),
        ]);
    }
}
```

### Rules

✅ **Always inject the interface** – never `new EloquentBetRepository()` or `Bet::query()` in Livewire/Actions  
✅ **Eager-load in the repository** – `->with([...])` lives here, not in callers  
✅ **Single responsibility** – repositories are query/persistence only; no business logic  
✅ **Swap for tests** – bind a fake in `TestCase::setUp()` for isolation  
❌ **No filtering/sorting passed as raw strings** – use typed parameters or Criteria objects  
❌ **No business decisions inside a repository** – no `if ($isBanned)` here  

---

## 8. Livewire Best Practices

### Component Structure

```php
class PlaceBetModal extends Component
{
    #[Validate('required|integer|min:1')]
    public int $amount = 0;
    
    public BetOption $option;
    
    public function placeBet(PlaceBetAction $action): void { ... }
    public function closeModal(): void { ... }
    
    public function render() { ... }
}
```

### Rules

✅ **Use #[Validate] attributes** on properties
```php
#[Validate('required|email')]
public string $email;

#[Validate('integer|min:1|max:100000')]
public int $amount;
```

✅ **Type-hint all method parameters**
```php
public function store(PlaceBetAction $action, PlacedBetsFeed $feed): void
```

✅ **Use #[On] for event listeners**
```php
#[On('bet-placed')]
public function refreshFeed(): void { ... }
```

✅ **Dispatch events instead of direct method calls**
```php
$this->dispatch('refresh-placed-bets');
```

✅ **Use wire:navigate.hover for links**
```html
<a href="/bets/{{ $bet->id }}" wire:navigate.hover>
    View Bet
</a>
```

❌ **NO business logic in Livewire components**
❌ **NO direct database queries** (use Services)
❌ **NO comments** (code should be self-explanatory)

---

## 9. Authorization & Policies

### Policy Location & Naming

```
app/Policies/BetPolicy.php
app/Policies/UserBetPolicy.php
```

### Policy Methods

```php
class BetPolicy
{
    public function closeBet(User $user, Bet $bet): bool
    {
        return $user->id === $bet->creator_id;
    }
    
    public function deleteBet(User $user, Bet $bet): bool
    {
        return $user->id === $bet->creator_id && $bet->isOpen();
    }
}
```

### In Livewire/Actions

```php
// In Livewire
$this->authorize('closeBet', $this->bet);

// In Action
$this->authorizesRequests->authorize('closeBet', $bet);

// Or use Gate directly
if (Gate::denies('closeBet', $bet)) {
    throw new UnauthorizedException('...');
}
```

---

## 10. Error Handling

### Exception Pattern

Create domain-specific exceptions:

```php
namespace App\Exceptions;

class BetException extends Exception {}
class InsufficientBalanceException extends BetException {}
class InvalidBetOptionException extends BetException {}
```

### In Services/Actions

```php
public function validateBalance(User $user, int $amount): void
{
    if ($user->soapnuts < $amount) {
        throw new InsufficientBalanceException(
            "User balance {$user->soapnuts} is less than required {$amount}"
        );
    }
}
```

### In Livewire

```php
try {
    $userBet = $action->execute(auth()->user(), $option, $amount);
    session()->flash('success', '🎉 Bet placed successfully!');
} catch (InsufficientBalanceException $e) {
    session()->flash('error', "Insufficient balance: {$e->getMessage()}");
} catch (BetException $e) {
    session()->flash('error', $e->getMessage());
}
```

---

## 11. Testing

### Test Organization

```
tests/
├── Feature/           # Integration tests (database, HTTP)
│   ├── Betting/
│   └── Auth/
├── Unit/              # Unit tests (services, actions)
│   ├── Actions/
│   └── Services/
└── TestCase.php       # Base test class
```

### Test Naming

- **Feature tests**: `test{Feature}{Scenario}` → `testPlaceBetWithValidData()`
- **Unit tests**: `test{Method}{Scenario}` → `testValidateBalanceThrowsException()`

### Test Structure

```php
/** @test */
public function placeBetDeductsBalance(): void
{
    $user = User::factory()->create(['soapnuts' => 1000]);
    $option = BetOption::factory()->create(['odds' => 2.0]);
    
    $userBet = (new PlaceBetAction(...))->execute($user, $option, 100);
    
    $this->assertEquals(900, $user->fresh()->soapnuts);
    $this->assertEquals(100, $userBet->amount_wagered);
}
```

---

## 12. Database & Models

### Model Relationships

```php
class Bet extends Model
{
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function options(): HasMany
    {
        return $this->hasMany(BetOption::class);
    }
    
    public function userBets(): HasMany
    {
        return $this->hasMany(UserBet::class);
    }
}
```

### Casts

```php
protected $casts = [
    'status' => BetStatus::class,
    'amount' => 'decimal:2',
    'expires_at' => 'datetime',
];
```

### Query Scopes (for common filters)

```php
class Bet extends Model
{
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', BetStatus::Open);
    }
    
    public function scopeCreatedBy(Builder $query, User $user): Builder
    {
        return $query->where('creator_id', $user->id);
    }
}

// Usage
$openBets = Bet::open()->createdBy($user)->get();
```

### Eager Loading

**Always** eager-load relationships in repositories, never rely on lazy loading:

```php
// In EloquentBetRepository – load what callers will need
public function paginateOpen(int $perPage = 20): LengthAwarePaginator
{
    return Bet::where('status', BetStatus::Open)
        ->with(['creator', 'betOptions', 'betOptions.userBets'])
        ->latest()
        ->paginate($perPage);
}
```

**In Livewire components**, never call `$bet->creator` without a prior `->load()` or `with()`:

```php
// Bad – triggers N+1 query per bet in a loop
@foreach($bets as $bet)
    {{ $bet->creator->name }}
@endforeach

// Good – loaded upfront in repository
$bets = $this->betRepository->paginateOpen();
```

### Database Transactions

Wrap every operation that touches **multiple models** in a transaction:

```php
// In an Action – guarantee atomicity
public function execute(PlaceBetData $data): UserBet
{
    return DB::transaction(function () use ($data): UserBet {
        $this->balance->decrementBalance($data->user, $data->amount);

        return UserBet::create([
            'user_id'           => $data->user->id,
            'bet_option_id'     => $data->option->id,
            'amount_wagered'    => $data->amount,
            'potential_winnings' => $potentialWinnings,
        ]);
    });
}
```

Rule of thumb: **one Action = one transaction**.

### Model Observers

Use Observers for **model lifecycle side effects** (logging, cache invalidation, notifications) — not for business logic.

```php
// app/Observers/BetObserver.php
<?php declare(strict_types=1);

namespace App\Observers;

use App\Models\Bet;

final class BetObserver
{
    public function created(Bet $bet): void
    {
        cache()->forget("bets.open.count");
    }

    public function updated(Bet $bet): void
    {
        if ($bet->isDirty('status')) {
            cache()->forget("bets.open.count");
        }
    }
}
```

Register in `AppServiceProvider::boot()`:

```php
use App\Models\Bet;
use App\Observers\BetObserver;

Bet::observe(BetObserver::class);
```

### Rules

✅ **Final models** – seal `final class Bet extends Model` to prevent inheritance abuse  
✅ **`protected function casts()`** – use method form (Laravel 10+), not `$casts` property  
✅ **UUIDs** – use `HasUuids` trait; set `$keyType = 'string'`, `$incrementing = false`  
✅ **Scopes for common filters** – `scopeOpen()`, `scopeCreatedBy()`  
❌ **No business logic in models** – `isOpen()` is a thin state check, not orchestration  
❌ **No raw queries in Livewire/Actions** – always go through Repository  

---

## 13. Events & Listeners

Use Laravel events for **decoupled side effects** — things that happen *because* of a business operation, not *as part* of it.

### When to Use Events

| Use events | Use direct call |
|---|---|
| Sending notifications | Deducting balance |
| Logging domain actions | Validating a bet |
| Cache invalidation | Calculating winnings |
| Cross-domain side effects | Core workflow steps |

### Event Definition

```php
<?php declare(strict_types=1);

namespace App\Events;

use App\Models\Bet;
use App\Models\BetOption;
use Illuminate\Foundation\Events\Dispatchable;

final class BetClosed
{
    use Dispatchable;

    public function __construct(
        public readonly Bet $bet,
        public readonly BetOption $winningOption,
    ) {}
}
```

### Listener

```php
<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\BetClosed;
use Illuminate\Contracts\Queue\ShouldQueue;

final class PayOutWinnersWhenBetClosed implements ShouldQueue
{
    public function handle(BetClosed $event): void
    {
        // iterate $event->bet->userBets, credit winners
    }
}
```

### Dispatching

```php
// In the Action, after all business logic succeeds
BetClosed::dispatch($bet, $winningOption);
```

### Rules

✅ **Past-tense event names** – `BetClosed`, `BetPlaced` (things that already happened)  
✅ **`ShouldQueue`** on listeners that do I/O (email, external APIs)  
✅ **Event carries only model instances** – no primitive soup  
✅ **Register in `EventServiceProvider`** (or use `#[AsEventListener]` in Laravel 11+)  
❌ **Never dispatch events from Listeners** – creates invisible chains  
❌ **Never put business logic in Listeners** – delegate to an Action  

---

## 14. Modern PHP Patterns

Use PHP 8.1+ features to write denser, clearer code.

### Enums (use instead of constants)

```php
enum BetStatus: string
{
    case Open   = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open   => 'Offen',
            self::Closed => 'Geschlossen',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open   => 'green',
            self::Closed => 'red',
        };
    }
}
```

### Match Expressions (replace switch/if chains)

```php
// Bad
if ($status === 'open') {
    $color = 'green';
} elseif ($status === 'closed') {
    $color = 'red';
} else {
    $color = 'gray';
}

// Good
$color = match ($status) {
    BetStatus::Open   => 'green',
    BetStatus::Closed => 'red',
    default           => 'gray',
};
```

### Constructor Promotion + Readonly

```php
final class PlaceBetData
{
    public function __construct(
        public readonly User $user,
        public readonly BetOption $option,
        public readonly int $amount,
    ) {}
}
```

### Named Arguments (for clarity at call sites)

```php
// Bad – what do these booleans mean?
$this->createBet($user, true, false, 'Wer gewinnt?');

// Good
$this->createBet(
    user: $user,
    dynamicOdds: true,
    private: false,
    title: 'Wer gewinnt?',
);
```

### Null-Safe Operator (sparingly)

```php
// Fine for optional chain
$label = $bet->winningOption?->title;

// Bad – hides a logic error; if null is unexpected, check explicitly
if ($user !== null && $user->isAdmin()) { ... }
```

---

## 15. No Comments Rule

**Code that requires comments is code that needs refactoring.**

If you feel the need to write a comment, the code itself isn't clear enough. Instead:

1. **Use semantic naming**
   ```php
   // Bad
   $b = 0.5; // discount multiplier
   $p = $x * $b; // apply discount
   
   // Good
   $discountMultiplier = 0.5;
   $discountedPrice = $basePrice * $discountMultiplier;
   ```

2. **Extract to methods**
   ```php
   // Bad
   if ($user->age > 18 && $user->verified && $balance > 0) { ... }
   
   // Good
   if ($this->isEligibleToBet($user)) { ... }
   ```

3. **Use well-named variables**
   ```php
   // Bad
   $r = $a * $b; // calculate result
   
   // Good
   $potentialWinnings = $odds * $amount;
   ```

4. **Name classes/methods by intent**
   ```php
   // Bad
   class Helper { ... } // What does this do?
   
   // Good
   class BettingValidationService { ... } // Clear purpose
   ```

### Exceptions to the No-Comment Rule

Only comment **why** something is done (not **what** or **how**):

```php
// GOOD – Explains business reasoning
public function isEligibleToBet(User $user): bool
{
    // Users must have verified email per company policy
    return $user->hasVerifiedEmail();
}

// BAD – Explains what the code does (obvious from code)
public function isEligibleToBet(User $user): bool
{
    // Check if user has verified email
    return $user->hasVerifiedEmail();
}
```

---

## 16. Code Review Checklist

Before committing, verify:

- [ ] All methods have return type declarations
- [ ] All parameters are type-hinted
- [ ] All properties have access modifiers and types
- [ ] No abbreviations in variable/method names
- [ ] Classes follow naming convention (Action, Service, Repository, DTO, Policy)
- [ ] Multi-model writes wrapped in `DB::transaction()`
- [ ] Relationships eager-loaded in Repository, not in Livewire/Actions
- [ ] No business logic in Livewire components or Listeners
- [ ] No direct DB queries in Livewire (go through Repository)
- [ ] DTOs used at all layer boundaries instead of plain arrays
- [ ] `readonly` on all DTO properties
- [ ] Repository interface injected, never Eloquent Model class directly
- [ ] #[Validate] used instead of inline validation in Livewire
- [ ] #[On] used for event listeners in Livewire
- [ ] Exceptions thrown for errors (no silent failures)
- [ ] No comments (code is self-documenting)
- [ ] PHPStan passes: `composer run analyse`
- [ ] Pint passes: `composer run lint:check`
- [ ] Tests pass: `composer test`

---

## 17. Quick Reference

### File Templates

**Action:**
```php
<?php declare(strict_types=1);

namespace App\Actions\Betting;

use App\Models\User;
use App\Models\Bet;

class PlaceBetAction
{
    public function __construct(
        private BettingValidationService $validation,
    ) {}
    
    public function execute(User $user, Bet $bet, int $amount): void
    {
        $this->validation->validateBalance($user, $amount);
        // Implementation
    }
}
```

**Service:**
```php
<?php declare(strict_types=1);

namespace App\Services\Betting;

class BetCalculationService
{
    public function calculateWinnings(float $odds, int $amount): int
    {
        return (int) ($odds * $amount);
    }
}
```

**Policy:**
```php
<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Bet;

class BetPolicy
{
    public function closeBet(User $user, Bet $bet): bool
    {
        return $user->id === $bet->creator_id;
    }
}
```

**Livewire Component:**
```php
<?php declare(strict_types=1);

namespace App\Livewire\Bets;

use Livewire\Attributes\Validate;
use Livewire\Component;

class PlaceBetModal extends Component
{
    #[Validate('integer|min:1')]
    public int $amount = 0;
    
    public function placeBet(PlaceBetAction $action): void
    {
        // Implementation
    }
    
    public function render() { ... }
}
```

**DTO:**
```php
<?php declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\BetOption;
use App\Models\User;

final class PlaceBetData
{
    public function __construct(
        public readonly User $user,
        public readonly BetOption $option,
        public readonly int $amount,
    ) {}

    public static function make(User $user, BetOption $option, int $amount): self
    {
        return new self(user: $user, option: $option, amount: $amount);
    }
}
```

**Repository Interface:**
```php
<?php declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bet;
use Illuminate\Pagination\LengthAwarePaginator;

interface BetRepositoryInterface
{
    public function findById(string $id): ?Bet;

    /** @return LengthAwarePaginator<Bet> */
    public function paginateOpen(int $perPage = 20): LengthAwarePaginator;

    public function save(Bet $bet): Bet;

    public function delete(Bet $bet): void;
}
```

**Event:**
```php
<?php declare(strict_types=1);

namespace App\Events;

use App\Models\Bet;
use Illuminate\Foundation\Events\Dispatchable;

final class BetPlaced
{
    use Dispatchable;

    public function __construct(
        public readonly Bet $bet,
    ) {}
}
```

---

## Summary

This is how we build Tipinuss:

1. **Structure** – Clear folder organization by domain; `Repositories/`, `DTOs/`, `ValueObjects/` alongside Actions and Services
2. **Naming** – Semantic, verb-first, no abbreviations; past-tense events, interface-suffixed repository contracts
3. **Types** – Strict typing on everything; `readonly` DTOs; PHP 8.1+ enums and match
4. **Separation** – Actions (operations), Services (logic), Repositories (data access), DTOs (data carriers), Livewire (UI only)
5. **Repository Pattern** – All data access behind interfaces; swap implementations without changing callers
6. **DTOs** – Typed, immutable data at every layer boundary; no raw `array $data`
7. **Value Objects** – Self-validating domain primitives (Soapnuts, Odds); equality by value
8. **Events** – Past-tense, past the business operation; listeners handle side effects and are queued
9. **Transactions** – One Action = one `DB::transaction()`
10. **No Comments** – Self-documenting code through intent
11. **Testing** – Comprehensive, organized by concern; fake repositories for unit tests
12. **Authorization** – Policies for all access control
13. **Quality** – PHPStan level 5, Pint, tests required before every commit

**Code is read 10x more than it's written. Optimize for humans.**

---

*Last updated: May 2025*
*Community-driven quality standards for Tipinuss*
