<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrukturOrganisasi extends Model
{
    protected $fillable = ['parent_id', 'jenis_hubungan', 'jabatan', 'nama_pejabat', 'foto_pejabat', 'urutan'];

    public function parent()
    {
        return $this->belongsTo(StrukturOrganisasi::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(StrukturOrganisasi::class, 'parent_id')->orderBy('urutan', 'asc');
    }

    public static function getTreeData($items = null, $parentId = null) {
        if (!$items) {
            $items = self::orderBy('id', 'asc')->get();
        }
        $branch = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $fotoUrl = $item->foto_pejabat ? \Illuminate\Support\Facades\Storage::url($item->foto_pejabat) : 'https://ui-avatars.com/api/?name='.urlencode($item->nama_pejabat ?? $item->jabatan).'&background=3b82f6&color=fff';
                
                $node = [
                    'id' => (string)$item->id,
                    'name' => $item->nama_pejabat ?? '-',
                    'title' => $item->jabatan,
                    'jenis' => $item->jenis_hubungan,
                    'img' => $fotoUrl
                ];
                
                $children = self::getTreeData($items, $item->id);
                if ($children) {
                    $node['children'] = $children;
                }
                $branch[] = $node;
            }
        }
        return count($branch) > 0 && $parentId == null ? $branch[0] : $branch;
    }
}
