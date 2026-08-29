<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
    use HasFactory;

    public static function getLookupCode($lookupId = 0){
        $language_id	        =   getCurrentLanguage();
        $returnVal 			=   Lookup::where('lookups.is_active', 1)->where("lookups.id",$lookupId)
        ->leftjoin('lookup_discriptions','lookup_discriptions.parent_id','lookups.id')
        ->where('lookup_discriptions.language_id',$language_id)
        ->value('lookup_discriptions.code');
        return !empty($returnVal) ? $returnVal : ''; 

    }

    public static function getLookupValues($lookup_type = 0){
        $language_id	    =   getCurrentLanguage();
        return Lookup::where('lookups.is_active', 1)->where("lookups.lookup_type",$lookup_type)
                                ->leftjoin('lookup_discriptions','lookup_discriptions.parent_id','lookups.id')
                                ->where('lookup_discriptions.language_id',$language_id)
                                ->pluck('lookup_discriptions.code','lookups.id')->toArray();

    }
}
