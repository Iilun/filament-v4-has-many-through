# Bug when using HasManyThrough in filament v4

This is a minimal app to reproduce the bug that occurs when using HasManyThrough and overriding getEloquentQuery

## What is in this ?

The idea here is to filter records by user on the list page using a HasManyThrough relationship.

    Users -> Throughs -> Items 

The two main changes to the default app are the HasManyThrough relation in `App\Models\User.php`

```php
public function items(): HasManyThrough 
{
    return $this->hasManyThrough(Item::class, Through::class);
}
```

And the override of `getEloquentQuery` in the resource

```php
public static function getEloquentQuery(): Builder
{
    return Auth::user()->items()->getQuery();
}
```

## Reproducing

### Setup

Running the seeding should suffice.

```shell
php artisan migrate --seed
```

### V3

Checkout the `v3` branch.

```shell
composer install
php artisan serve
```

Go to `http://localhost:8000`. Login with `test@example.com` and `password`.

The 6 expected records are there, and each is unique.

### V4

Checkout the `v4` branch.

Clear the views if needed.

```shell
composer install
php artisan view:clear
php artisan serve
```

Go to `http://localhost:8000`. Login with `test@example.com` and `password`.

The 6 expected records are there, **BUT these are duplicated based on the `through_id`**. Only the first record for each id is displayed, and duplicated.


## What I know

When running tinker queries, we can find the root cause. When using HasManyThrough->getQuery()->get(), the `id` field from the model is indeed the same as `trough_id`.

Extracts from tinker: 

```shell
> User::first()->items()->getQuery()->limit(2)->get()
= Illuminate\Database\Eloquent\Collection {#7082
    all: [
      App\Models\Item {#7072
        id: 1,
        through_id: 1,
        name: "Harley Davis",
        created_at: "2025-07-26 20:40:21",
        updated_at: "2025-07-26 20:40:21",
        user_id: 1,
      },
      App\Models\Item {#7083
        id: 1,
        through_id: 1,
        name: "Connie Carter",
        created_at: "2025-07-26 20:40:21",
        updated_at: "2025-07-26 20:40:21",
        user_id: 1,
      },
    ],
  }
```

What's interesting is that this is **not** the case when querying the relation directly (without getQuery)

```shell
> User::first()->items()->limit(2)->get()
= Illuminate\Database\Eloquent\Collection {#7056
    all: [
      App\Models\Item {#7057
        id: 1,
        through_id: 1,
        name: "Harley Davis",
        created_at: "2025-07-26 20:40:21",
        updated_at: "2025-07-26 20:40:21",
        laravel_through_key: 1,
      },
      App\Models\Item {#7103
        id: 2,
        through_id: 1,
        name: "Connie Carter",
        created_at: "2025-07-26 20:40:21",
        updated_at: "2025-07-26 20:40:21",
        laravel_through_key: 1,
      },
    ],
  }
```
This seems like a weird behavior from laravel, but it did work in v3.

