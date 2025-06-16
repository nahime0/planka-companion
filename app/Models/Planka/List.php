<?php

namespace App\Models\Planka;

/**
 * Alias for ListModel class
 * 
 * Since "list" is a reserved word in PHP, the actual model class is named ListModel.
 * This file provides a convenient alias for better naming consistency with Planka's database.
 */
class_alias(ListModel::class, List::class);