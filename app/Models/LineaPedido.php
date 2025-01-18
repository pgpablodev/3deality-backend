<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineaPedido extends Model
{
    use HasFactory;
    protected $table = 'lineas_pedido';
    protected $fillable = ['id_pedido', 'id_articulo', 'cantidad'];
}
