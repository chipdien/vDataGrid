# CLAUDE.md - vDataGrid Project Guide for AI Assistants

## Project Overview

**Project Name**: vDataGrid
**Type**: PHP Library (Enterprise DataGrid System)
**Namespace**: `VGrid\`
**PHP Version**: ^8.1
**Primary Dependencies**: Laravel Illuminate packages (Database, Support)
**UI Framework**: Bootstrap 5
**License**: MIT

**Description**: An enterprise-grade PHP DataGrid and Form library with smart columns, view overrides, and a fluent API. Supports dynamic data providers, extensive column types, form inputs with repeater fields, and a resource pattern for DRY field definitions.

---

## Core Architecture

### Design Patterns

1. **Component Pattern**: Self-contained, reusable components (Columns, Inputs, Actions)
2. **Fluent/Chainable API**: Method chaining for configuration (`->sortable()->required()->span(6)`)
3. **View Resolver Pattern**: Flexible template system with override capability
4. **Resource Pattern**: DRY field definitions reusable across forms and grids
5. **Data Provider Pattern**: Abstraction layer for data sources (SQL, API, etc.)
6. **Trait-based Composition**: Shared behavior through traits (ViewResolver)
7. **Interface Contracts**: Clear contracts for extensibility (Renderable, ColumnInterface, DataProviderInterface)

### Key Principles

- **Single Responsibility**: Each component has one clear purpose
- **Open/Closed**: Easy to extend (new column/input types) without modifying core
- **Dependency Inversion**: Depend on interfaces (DataProviderInterface)
- **Composition over Inheritance**: Traits for shared behavior
- **Lazy Evaluation**: Data fetched only when rendering
- **Graceful Degradation**: Missing views/invalid data handled safely

---

## Directory Structure

```
/home/user/vDataGrid/
├── src/                              # Source code (PSR-4: VGrid\)
│   ├── Grid/
│   │   └── DataGrid.php              # Main table component
│   ├── Form/
│   │   └── DataForm.php              # Main form component
│   ├── Components/
│   │   ├── Columns/                  # 9 column types
│   │   │   ├── AbstractColumn.php    # Base class (extend for new columns)
│   │   │   ├── TextColumn.php
│   │   │   ├── StatusColumn.php
│   │   │   ├── CurrencyColumn.php
│   │   │   ├── DateTimeColumn.php
│   │   │   ├── BooleanColumn.php
│   │   │   ├── LinkColumn.php
│   │   │   ├── ImageColumn.php
│   │   │   └── ActionColumn.php
│   │   ├── Inputs/                   # 6 input types
│   │   │   ├── AbstractInput.php     # Base class (extend for new inputs)
│   │   │   ├── TextInput.php
│   │   │   ├── SelectInput.php
│   │   │   ├── DateInput.php
│   │   │   ├── TextareaInput.php     # Vditor WYSIWYG + LaTeX support
│   │   │   └── RepeaterInput.php     # Dynamic repeating fields
│   │   └── Actions/
│   │       ├── AbstractAction.php
│   │       └── Button.php
│   ├── Data/
│   │   └── SqlDataProvider.php       # Laravel Query Builder wrapper
│   ├── Resources/
│   │   ├── BaseResource.php          # Abstract base for resources
│   │   └── ClassResource.php         # Example resource implementation
│   ├── Contracts/
│   │   ├── Renderable.php
│   │   ├── ColumnInterface.php
│   │   └── DataProviderInterface.php
│   └── Traits/
│       └── ViewResolver.php          # View override mechanism
├── views/bootstrap5/                 # Bootstrap 5 templates
│   ├── grid.php                      # Main grid template
│   ├── form.php                      # Main form template
│   ├── columns/                      # Column view templates
│   ├── inputs/                       # Input view templates
│   └── actions/                      # Action view templates
├── composer.json                     # Dependencies & autoload config
├── index.php                         # Demo file (classes grid)
├── demo2.php                         # Demo file
└── demo3.php                         # Demo file
```

---

## Core Components Deep Dive

### 1. DataGrid (`src/Grid/DataGrid.php`)

**Purpose**: Render paginated, sortable data tables

**Usage Pattern**:
```php
$grid = new DataGrid('unique_id');
$grid->setDataProvider($provider)
     ->setColumns([
         TextColumn::make('name', 'Full Name'),
         StatusColumn::make('status', 'Status')->options([...]),
     ]);
echo $grid->render();
```

**Key Methods**:
- `setColumns(array $columns)` - Define table columns
- `setDataSource(array $rows)` - Static data array
- `setDataProvider(DataProviderInterface $provider)` - Dynamic data (DB/API)
- `render(array $data = [])` - Generate HTML output

**Request Handling**:
- URL parameters: `?{grid_id}_page=2` for pagination
- Automatically calls `handleRequest()` before rendering
- Delegates pagination to DataProvider

**View**: `views/bootstrap5/grid.php`

---

### 2. DataForm (`src/Form/DataForm.php`)

**Purpose**: Render forms for creating/editing records

**Usage Pattern**:
```php
$form = new DataForm('/save');
$form->setInputs([
    TextInput::make('name', 'Name')->required(),
    SelectInput::make('status', 'Status')->options([...]),
]);

// For edit mode:
$form->bindData($existingRecord);

echo $form->render();
```

**Key Methods**:
- `setInputs(array $inputs)` - Define form fields
- `bindData(array $data)` - Populate form (edit mode)
- `save(string $table, ?int $id = null)` - Direct DB save helper
- `render(array $params = [])` - Generate HTML output

**Grid Layout**: Bootstrap 12-column system
- `->span(6)` = 50% width (col-md-6)
- `->span(12)` = 100% width (default)
- Responsive: `col-12 col-md-{span}`

**View**: `views/bootstrap5/form.php`

---

### 3. Column Types

**Hierarchy**:
```
AbstractColumn (implements ColumnInterface)
├── TextColumn       - Plain text with optional truncation/copy
├── StatusColumn     - Colored badges (Bootstrap contextual classes)
├── CurrencyColumn   - Formatted money with +/- colorization
├── DateTimeColumn   - Formatted dates (PHP date format strings)
├── BooleanColumn    - Icon-based true/false (check/x)
├── LinkColumn       - Clickable hyperlinks
├── ImageColumn      - Image display with sizing/styling
└── ActionColumn     - Container for Button actions
```

**Common Column Methods** (chainable):
- `make(string $field, string $label)` - Static factory
- `sortable()` - Enable column sorting
- `limit(int $chars)` - Truncate display
- `format(callable $fn)` - Custom formatting callback
- `setView(string $path)` - Override default view

**Example - StatusColumn**:
```php
StatusColumn::make('status', 'Status')
    ->options([
        'active' => ['label' => 'Active', 'class' => 'success'],
        'pending' => ['label' => 'Pending', 'class' => 'warning'],
        'inactive' => ['label' => 'Inactive', 'class' => 'danger'],
    ])
```

**Interface Contract**:
```php
interface ColumnInterface extends Renderable {
    public function getName(): string;
    public function getLabel(): string;
    public function renderCell(array $row): string;
    public function renderFilter(string $location): string;
}
```

---

### 4. Input Types

**Hierarchy**:
```
AbstractInput (implements Renderable)
├── TextInput        - text, password, email, number, etc.
├── SelectInput      - Dropdown with key-value options
├── DateInput        - HTML5 date picker
├── TextareaInput    - Multi-line text (plain or Vditor WYSIWYG)
└── RepeaterInput    - Dynamic repeating field groups
```

**Common Input Methods** (chainable):
- `make(string $name, string $label)` - Static factory
- `required()` - Mark field as required
- `span(int $colSpan)` - Grid column width (1-12)
- `setValue(mixed $value)` - Set current value
- `placeholder(string $text)` - Input placeholder

**Example - TextInput**:
```php
TextInput::make('email', 'Email Address')
    ->type('email')
    ->required()
    ->span(6)
    ->placeholder('user@example.com')
```

**Example - RepeaterInput**:
```php
RepeaterInput::make('items', 'Line Items')
    ->schema([
        TextInput::make('product', 'Product')->span(6),
        TextInput::make('quantity', 'Qty')->span(3)->type('number'),
        TextInput::make('price', 'Price')->span(3)->type('number'),
    ])
```

**RepeaterInput Technical Details**:
- Generates nested array names: `items[0][product]`, `items[1][product]`
- Uses HTML `<template>` for client-side row cloning
- JavaScript functions: `addRepeaterItem()`, `removeRepeaterItem()`
- Clones input objects server-side to avoid mutation

---

### 5. Action System

**Components**:
- `AbstractAction` - Base class
- `Button` - Concrete button implementation

**Button Features**:
```php
Button::make('Edit')
    ->icon('bi bi-pencil')                // Bootstrap Icon
    ->class('btn-primary')                // Bootstrap button class
    ->url(fn($row) => "/edit/{$row['id']}") // Dynamic URL callback
    ->confirm('Delete this record?')      // JS confirmation
    ->canSee(fn($row) => $row['editable']) // Conditional visibility
    ->openNewTab()                        // target="_blank"
```

**Usage in Grid**:
```php
ActionColumn::make('actions', 'Actions')
    ->actions([
        Button::make('Edit')->url(fn($r) => "/edit/{$r['id']}"),
        Button::make('Delete')
            ->class('btn-danger')
            ->confirm('Are you sure?')
            ->canSee(fn($r) => $r['deleteable']),
    ])
```

---

### 6. Data Providers

**Interface**:
```php
interface DataProviderInterface {
    public function getData(): array;
    public function getTotalCount(): int;
    public function paginate(int $page, int $perPage): self;
    public function sort(string $field, string $direction): self;
}
```

**SqlDataProvider** (`src/Data/SqlDataProvider.php`):
- Wraps Laravel Query Builder
- Chainable methods (fluent interface)
- Security: SQL injection prevention via regex validation + parameterization
- Returns plain arrays (not Eloquent models)

**Usage**:
```php
use Illuminate\Database\Capsule\Manager as DB;

$query = DB::table('users')
    ->select(['id', 'name', 'email', 'status'])
    ->where('active', true);

$provider = new SqlDataProvider($query);
$provider->paginate(1, 20);

$grid->setDataProvider($provider);
```

---

### 7. Resource Pattern

**Purpose**: DRY field definitions - define once, use everywhere

**Structure**:
```php
class ClassResource extends BaseResource {
    // 1. Atomic field definitions
    public static function fieldCode() {
        return TextInput::make('class_code', 'Mã Lớp')->required();
    }

    public static function fieldName() {
        return TextInput::make('class_name', 'Tên Lớp')->required();
    }

    // 2. Form compositions
    public static function formQuickAdd(): array {
        return [
            self::fieldCode()->span(6),
            self::fieldName()->span(6),
        ];
    }

    public static function formFullEdit(): array {
        return [
            self::fieldCode(),
            self::fieldName(),
            // ... more fields
        ];
    }

    // 3. Table definitions
    public static function tableColumns(): array {
        return [
            TextColumn::make('class_code', 'Mã'),
            TextColumn::make('class_name', 'Tên'),
            StatusColumn::make('status', 'Trạng Thái')->options([...]),
        ];
    }
}
```

**Benefits**:
- Single source of truth
- Consistency across CRUD operations
- Easy to update field properties globally
- Compose different forms from same base fields

---

### 8. View System

**ViewResolver Trait**:
```php
trait ViewResolver {
    protected ?string $customView = null;

    public function setView(string $path): self {
        $this->customView = $path;
        return $this;
    }

    protected function resolveView(string $defaultPath, array $data): string {
        $viewPath = $this->customView ?? $defaultPath;

        if (!file_exists($viewPath)) {
            return "<!-- View not found: {$viewPath} -->";
        }

        ob_start();
        extract($data);
        include $viewPath;
        return ob_get_clean();
    }
}
```

**View Override Mechanism**:
1. Component has default view path
2. User can call `->setView('/custom/path.php')` to override
3. `resolveView()` checks custom view first, then default
4. Graceful fallback if view missing

**Data Passing**:
```php
// In component:
return $this->resolveView('path/to/view.php', [
    'value' => $formattedValue,
    'row' => $currentRow,
]);

// In view (path/to/view.php):
<span><?= htmlspecialchars($value) ?></span>
```

---

## Development Workflows

### Creating a New Column Type

**Step 1**: Create class file
```php
// src/Components/Columns/MyColumn.php
namespace VGrid\Components\Columns;

class MyColumn extends AbstractColumn {
    protected string $specialProperty = '';

    public function specialConfig(string $value): self {
        $this->specialProperty = $value;
        return $this;
    }

    public function renderCell(array $row): string {
        $value = $row[$this->name] ?? '';
        // Process value...

        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/my_column.php',
            ['value' => $value, 'row' => $row]
        );
    }
}
```

**Step 2**: Create view template
```php
// views/bootstrap5/columns/my_column.php
<?php
/** @var string $value */
/** @var array $row */
?>
<div class="my-custom-column">
    <?= htmlspecialchars($value) ?>
</div>
```

**Step 3**: Use in grid
```php
use VGrid\Components\Columns\MyColumn;

$grid->setColumns([
    MyColumn::make('field_name', 'Label')->specialConfig('value'),
]);
```

---

### Creating a New Input Type

**Step 1**: Create class file
```php
// src/Components/Inputs/MyInput.php
namespace VGrid\Components\Inputs;

class MyInput extends AbstractInput {
    protected string $customOption = '';

    public function customOption(string $value): self {
        $this->customOption = $value;
        return $this;
    }

    protected function getViewPath(): string {
        return __DIR__ . '/../../../views/bootstrap5/inputs/my_input.php';
    }
}
```

**Step 2**: Create view template
```php
// views/bootstrap5/inputs/my_input.php
<?php
/** @var MyInput $field */
?>
<div class="mb-3">
    <label class="form-label">
        <?= htmlspecialchars($field->label) ?>
        <?php if ($field->required): ?>
            <span class="text-danger">*</span>
        <?php endif; ?>
    </label>
    <input
        type="text"
        name="<?= htmlspecialchars($field->name) ?>"
        value="<?= htmlspecialchars($field->value ?? '') ?>"
        class="form-control"
        <?= $field->required ? 'required' : '' ?>
    />
</div>
```

**Step 3**: Use in form
```php
use VGrid\Components\Inputs\MyInput;

$form->setInputs([
    MyInput::make('field_name', 'Label')->customOption('value'),
]);
```

---

### Using Resources for DRY Code

**Define Resource**:
```php
// src/Resources/UserResource.php
namespace VGrid\Resources;

class UserResource extends BaseResource {
    public static function fieldEmail() {
        return TextInput::make('email', 'Email')
            ->type('email')
            ->required();
    }

    public static function fieldName() {
        return TextInput::make('name', 'Full Name')
            ->required();
    }

    public static function formCreate(): array {
        return [
            self::fieldName()->span(6),
            self::fieldEmail()->span(6),
        ];
    }

    public static function tableColumns(): array {
        return [
            TextColumn::make('name', 'Name'),
            TextColumn::make('email', 'Email'),
        ];
    }
}
```

**Use in Application**:
```php
// Create form
$form->setInputs(UserResource::formCreate());

// List grid
$grid->setColumns(UserResource::tableColumns());
```

---

### Database Integration

**Setup** (Laravel Illuminate):
```php
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'your_database',
    'username'  => 'your_username',
    'password'  => 'your_password',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
```

**Query & Provider**:
```php
use Illuminate\Database\Capsule\Manager as DB;
use VGrid\Data\SqlDataProvider;

$query = DB::table('users')
    ->select(['id', 'name', 'email', 'status'])
    ->where('deleted_at', null)
    ->orderBy('created_at', 'desc');

$provider = new SqlDataProvider($query);
$grid->setDataProvider($provider);
```

**Form Save**:
```php
// Simple save
$form->save('users', $id); // $id = null for INSERT, $id for UPDATE

// Manual save
$payload = [];
foreach ($form->inputs as $input) {
    $payload[$input->name] = $_POST[$input->name] ?? null;
}
DB::table('users')->insert($payload);
```

---

## Code Conventions

### Naming Conventions

**Files & Classes**:
- Classes: `PascalCase` (TextColumn.php, DataGrid.php)
- Views: `snake_case` (text.php, repeater.php)
- Directories: Plural for collections (Columns/, Inputs/, Actions/)

**Variables & Methods**:
- Methods: `camelCase` (sortable(), required(), renderCell())
- Properties: `camelCase` (protected $dataProvider)
- Database columns: `snake_case` (class_code, created_at)
- Constants: `UPPER_SNAKE_CASE` (though none currently used)

**Prefixes**:
- Getters: `getName()`, `getLabel()`, `getViewPath()`
- Setters: `setView()`, `setValue()`, `setColumns()`
- Factories: `make()` (static factory method)
- Booleans: `can...()`, `is...()`, `has...()`

---

### Method Signatures

**Static Factories**:
```php
public static function make(string $name, string $label): self
```

**Chainable Configuration**:
```php
public function required(): self
public function sortable(): self
public function span(int $span): self
```

**Callback Parameters**:
```php
public function format(callable $callback): self  // fn($row) => ...
public function url(callable $callback): self     // fn($row) => ...
public function canSee(callable $callback): self  // fn($row) => bool
```

**Rendering**:
```php
public function render(array $data = []): string
public function renderCell(array $row): string
```

---

### Security Best Practices

**Output Escaping**:
```php
// Always escape user data in views
<?= htmlspecialchars($value) ?>

// For JavaScript strings
<script>
const data = '<?= addslashes($value) ?>';
</script>
```

**SQL Injection Prevention**:
```php
// Use Query Builder (parameterized queries)
DB::table('users')->where('id', $id)->get();

// Validate column names for sorting
if (preg_match('/^[a-z0-9_]+$/i', $field)) {
    $query->orderBy($field, $direction);
}
```

**XSS Prevention**:
- `htmlspecialchars()` in all view outputs
- Bootstrap classes are whitelisted
- User input validated before rendering

---

### Error Handling

**Graceful Degradation**:
```php
// Missing view
if (!file_exists($viewPath)) {
    return "<!-- View not found: {$viewPath} -->";
}

// Missing data
$value = $row[$this->name] ?? '';

// Invalid options
$option = $this->options[$value] ?? ['label' => $value, 'class' => 'secondary'];
```

**Validation**:
```php
// Required fields (HTML5)
<input required>

// Type validation
TextInput::make('email')->type('email')
TextInput::make('age')->type('number')
```

---

## Important Technical Details

### Request/Response Flow

**DataGrid Rendering**:
1. User calls `$grid->render()`
2. `handleRequest()` reads URL parameters (`?grid_id_page=2`)
3. Parameters applied to DataProvider (`->paginate(2, 10)`)
4. DataProvider fetches data from database
5. Rows passed to view with columns
6. Each column calls `renderCell($row)` for each cell
7. Final HTML returned

**DataForm Rendering**:
1. User calls `$form->render()`
2. Optional: `bindData($record)` populates input values
3. Inputs iterated in view
4. Each input renders with `$input->render()`
5. Bootstrap grid layout applied (col-md-{span})
6. Final HTML returned

---

### Magic Methods

**__get in AbstractInput**:
```php
public function __get($prop) {
    if (property_exists($this, $prop)) {
        return $this->$prop;
    }
    return null;
}
```

**Purpose**: Allow views to access protected properties
- `$field->label` instead of `$field->getLabel()`
- `$field->required` instead of `$field->isRequired()`
- Cleaner view syntax

---

### Callback-Based Configuration

**Dynamic URLs**:
```php
Button::make('Edit')->url(fn($row) => "/edit/{$row['id']}")
```

**Conditional Rendering**:
```php
Button::make('Delete')->canSee(fn($row) => $row['can_delete'])
```

**Custom Formatting**:
```php
TextColumn::make('price')->format(fn($row) => '$' . number_format($row['price']))
```

**Benefits**:
- Flexible, context-aware configuration
- Closures have access to full row data
- Evaluated at render time (lazy)

---

### Object Cloning in Repeater

**Why Clone**:
```php
// Without cloning:
$input->setValue($value);  // Modifies original schema!

// With cloning:
$clone = clone $input;
$clone->setValue($value);  // Original unchanged
```

**Usage** (in RepeaterInput view):
```php
foreach ($existingValues as $index => $rowData) {
    foreach ($this->schema as $subInput) {
        $inputClone = clone $subInput;
        $inputClone->overrideName("{$this->name}[{$index}][{$subInput->name}]");
        $inputClone->setValue($rowData[$subInput->name]);
        echo $inputClone->render();
    }
}
```

---

### Type Handling & Flexibility

**Loose Comparisons**:
- StatusColumn: Uses `==` to match '1' with 1
- BooleanColumn: `filter_var($value, FILTER_VALIDATE_BOOLEAN)`
- Handles both string and native types from database

**Date Handling**:
```php
// DateTimeColumn
$timestamp = is_numeric($value) ? (int)$value : strtotime($value);
return date($this->format, $timestamp);
```

**Fallbacks**:
- Missing options: Use value as label
- Invalid dates: Return original string
- Missing images: Fallback image support

---

## Testing & Debugging

### Demo Files

**index.php**: Classes management grid
- Database connection (Illuminate Capsule)
- Seed data generation
- StatusColumn usage example
- Pagination demonstration

**demo2.php**: Additional features (check file for details)

**demo3.php**: Advanced examples (check file for details)

---

### Common Issues & Solutions

**Issue**: Column not rendering
- Check field name matches database column
- Verify column type is correct
- Ensure view file exists

**Issue**: Form not saving
- Check input names match database columns
- Verify database connection is active
- Inspect $_POST data

**Issue**: RepeaterInput JavaScript errors
- Ensure unique field names (no conflicts)
- Check browser console for specific error
- Verify template element exists in DOM

**Issue**: View override not working
- Use absolute path, not relative
- Verify file exists and is readable
- Check path in `setView()` matches file location

---

## Extension Points

### Custom Data Providers

**Implement Interface**:
```php
class ApiDataProvider implements DataProviderInterface {
    public function getData(): array {
        // Fetch from API
    }

    public function getTotalCount(): int {
        // Return total records
    }

    public function paginate(int $page, int $perPage): self {
        // Apply pagination
        return $this;
    }

    public function sort(string $field, string $direction): self {
        // Apply sorting
        return $this;
    }
}
```

**Use**:
```php
$provider = new ApiDataProvider('https://api.example.com/users');
$grid->setDataProvider($provider);
```

---

### Custom View Themes

**Create Theme Directory**:
```
views/
├── bootstrap5/          # Default theme
└── tailwind/            # Custom theme
    ├── grid.php
    ├── form.php
    ├── columns/
    └── inputs/
```

**Override Globally** (modify component):
```php
// In TextColumn.php
protected function getViewPath(): string {
    return __DIR__ . '/../../../views/tailwind/columns/text.php';
}
```

**Override Per Instance**:
```php
TextColumn::make('name', 'Name')
    ->setView('/path/to/views/tailwind/columns/text.php')
```

---

### Validation System (Future)

**Potential Pattern**:
```php
TextInput::make('email', 'Email')
    ->rules(['required', 'email', 'max:255'])
    ->messages([
        'email' => 'Invalid email format',
    ])
```

**Implementation** (not yet in codebase):
- Add `rules` and `messages` properties to AbstractInput
- Create Validator class
- Integrate with DataForm::save()

---

## Key Files Reference

| File | Purpose | When to Modify |
|------|---------|----------------|
| `src/Grid/DataGrid.php` | Main table component | Add grid-level features |
| `src/Form/DataForm.php` | Main form component | Add form-level features |
| `src/Components/Columns/AbstractColumn.php` | Base for columns | Add shared column behavior |
| `src/Components/Inputs/AbstractInput.php` | Base for inputs | Add shared input behavior |
| `src/Traits/ViewResolver.php` | View system | Modify view resolution logic |
| `src/Resources/BaseResource.php` | Resource pattern base | Add resource-level features |
| `src/Data/SqlDataProvider.php` | SQL data adapter | Modify query behavior |
| `src/Contracts/*.php` | Interfaces | Define new contracts |
| `views/bootstrap5/grid.php` | Grid template | Customize table layout |
| `views/bootstrap5/form.php` | Form template | Customize form layout |

---

## Development Checklist

### Adding New Column Type
- [ ] Create `src/Components/Columns/XxxColumn.php` extending `AbstractColumn`
- [ ] Implement `renderCell(array $row): string` method
- [ ] Add custom configuration methods (chainable)
- [ ] Create view file `views/bootstrap5/columns/xxx.php`
- [ ] Handle edge cases (missing data, invalid values)
- [ ] Test with DataGrid
- [ ] Document usage example

### Adding New Input Type
- [ ] Create `src/Components/Inputs/XxxInput.php` extending `AbstractInput`
- [ ] Implement `getViewPath(): string` method
- [ ] Add custom configuration methods (chainable)
- [ ] Create view file `views/bootstrap5/inputs/xxx.php`
- [ ] Support `required`, `span`, `value` properties
- [ ] Test with DataForm
- [ ] Document usage example

### Creating Resource
- [ ] Create `src/Resources/XxxResource.php` extending `BaseResource`
- [ ] Define atomic field methods (fieldName(), fieldEmail())
- [ ] Create form composition methods (formCreate(), formEdit())
- [ ] Create table column method (tableColumns())
- [ ] Use in actual forms and grids
- [ ] Verify DRY principle achieved

---

## Important Notes for AI Assistants

### Language Context
- **Codebase Language**: Vietnamese comments and documentation
- **User-facing**: Vietnamese labels, messages, and UI text
- **Code**: English naming (methods, classes, variables)
- **When modifying**: Maintain Vietnamese in user-facing strings, English in code

### Database Context
- **Example Database**: saas_centers (education management system)
- **Example Tables**: classes, tenants, subjects, courses
- **Connection**: Uses Laravel Illuminate Database (Capsule)
- **Credentials**: Hardcoded in demo files (should be env-based in production)

### Security Considerations
- **SQL Injection**: Mitigated by Laravel Query Builder parameterization + regex validation
- **XSS**: Mitigated by `htmlspecialchars()` in views
- **CSRF**: Not implemented (add token system for production)
- **Authentication**: Not included (application-level concern)

### Performance Notes
- **Lazy Loading**: Data fetched only on render
- **N+1 Prevention**: Use Query Builder select() to limit columns
- **Pagination**: Built-in via DataProvider
- **View Caching**: Not implemented (consider for production)

### Common Patterns
- **Fluent Interface**: Always return `$this` from configuration methods
- **Static Factories**: Use `make()` for clean instantiation
- **Callbacks**: Accept `callable` for dynamic behavior
- **Trait Composition**: Use traits for cross-cutting concerns

### Do's and Don'ts

**DO**:
- Extend `AbstractColumn` for new columns
- Extend `AbstractInput` for new inputs
- Use `htmlspecialchars()` in views
- Implement chainable methods
- Use Resource pattern for repeated field definitions
- Follow PSR-4 autoloading conventions

**DON'T**:
- Modify core Abstract classes (extend instead)
- Bypass Query Builder for raw SQL
- Output unescaped user data
- Break method chaining (always return `$this`)
- Duplicate field definitions (use Resources)
- Use `echo` in components (return strings)

---

## Recent Development History

**Latest Commits** (newest → oldest):
```
20c3324 feat(resources): implement resource pattern for class management
a25efcf feat(inputs): add repeater input component with dynamic fields
a576eb5 feat(inputs): add new input components and form handling
9d1d3e7 feat: add form components and action button system
721e198 feat(columns): add new column types and enhance text column
```

**Evolution**:
1. Basic DataGrid + TextColumn
2. Column types expansion (Status, Currency, DateTime, Boolean, Link, Image)
3. Action system (Button, ActionColumn)
4. Form system (DataForm, Input types)
5. RepeaterInput (complex dynamic fields)
6. Resource pattern (DRY field definitions)

**Current State**: Mature feature set, ready for production use with minor additions (CSRF, validation, more column types)

---

## Quick Start for AI Assistants

### Understanding a Feature Request

1. **Identify Component Type**: Column, Input, Action, or Core?
2. **Check Existing Patterns**: Similar component already exists?
3. **Determine Extension Point**: Extend Abstract class or modify core?
4. **Plan View Template**: Bootstrap 5 structure needed
5. **Consider Resource Usage**: Can this be part of a Resource?

### Implementing a New Feature

1. **Read Relevant Abstract Class**: Understand base behavior
2. **Review Similar Component**: Follow established patterns
3. **Create Component Class**: Extend appropriate base class
4. **Implement Required Methods**: Interface contracts
5. **Add Configuration Methods**: Chainable fluent methods
6. **Create View Template**: Bootstrap 5 markup
7. **Test with Demo**: Use in index.php or demo file
8. **Document Usage**: Add example to CLAUDE.md

### Debugging an Issue

1. **Check Component Type**: Column, Input, Form, Grid?
2. **Verify View Path**: File exists and path correct?
3. **Inspect Data**: Check `var_dump($row)` or `var_dump($_POST)`
4. **Check Browser Console**: JavaScript errors (especially RepeaterInput)?
5. **Review Database**: Correct column names and types?
6. **Test in Isolation**: Reproduce with minimal example

---

## Glossary

**Column**: Visual representation of a data field in DataGrid
**Input**: Form field in DataForm
**Action**: Button or clickable element (typically in ActionColumn)
**Resource**: Reusable field definition collection
**Provider**: Data source abstraction (SQL, API, etc.)
**View**: PHP template file for rendering HTML
**ViewResolver**: Trait handling view loading and overrides
**Fluent API**: Method chaining pattern (`->method1()->method2()`)
**Span**: Bootstrap grid column width (1-12)
**Repeater**: Dynamic repeating field group

---

## Version Information

**PHP**: 8.1+ (uses named arguments, modern syntax)
**Laravel Illuminate**: 10.0+ (Database, Support packages)
**Bootstrap**: 5.3.0+ (CSS framework)
**Bootstrap Icons**: 1.11.0+ (icon library)
**Vditor**: Optional (WYSIWYG editor in TextareaInput)
**KaTeX**: Optional (LaTeX math in TextareaInput)

---

## Useful Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Dump autoload (after adding new classes)
composer dump-autoload

# Run demo
php -S localhost:8000
# Then visit: http://localhost:8000/index.php
```

---

## Support & Resources

**Repository**: Check git remote for repository URL
**Issues**: Report bugs via repository issue tracker
**Documentation**: This CLAUDE.md file
**Examples**: index.php, demo2.php, demo3.php
**Tests**: Not yet implemented (TDD opportunity)

---

*This document is maintained for AI assistants working on the vDataGrid project. Last updated: 2025-12-01*
