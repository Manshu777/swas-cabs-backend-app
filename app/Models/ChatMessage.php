<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    // Point the model to your custom table name
    protected $table = 'message_with_driver_user';

    protected $fillable = ['ride_id', 'sender_id', 'message', 'is_read'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
}