<?php
namespace yentu\database;

use yentu\database\Table;
use yentu\database\Schema;
use yentu\database\Column;
use yentu\database\ForeignKey;
use yentu\database\UniqueKey;
use yentu\database\Index;
use yentu\database\View;
use yentu\database\Query;
use yentu\database\Begin;
use yentu\database\PrimaryKey;

enum ItemType: string
{
    case Begin = Begin::class;
    case Table = Table::class;
    case Schema = Schema::class;
    case Column = Column::class;
    case ForeignKey = ForeignKey::class;
    case UniqueKey = UniqueKey::class;
    case Index = Index::class;
    case View = View::class;
    case Query = Query::class;
    case PrimaryKey = PrimaryKey::class;
}
