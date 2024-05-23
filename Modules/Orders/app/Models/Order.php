<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Orders\Database\Factories\OrderFactory;
use Modules\Orders\Models\Purchase;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = DB_CONNECTION_DEFAULT;
    protected $table = 'orders';
    protected $fillable = ['contacts', 'client_id', 'sum', 'note', 'is_online', 'pay_id', 'pay_url', 'status', 'payment_provider'];

    protected static function newFactory()
    {
        return \Modules\Orders\Database\factories\OrderFactory::new();
    }

    /**
     * Generate a new UUID for the model.
     */
    public function newUniqueId(): string
    {
        return (string) Uuid::uuid4();
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

}
