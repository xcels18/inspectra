<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MasterOpd;
use App\Models\PermintaanOpd;

class FixOrphanOpd extends Command
{
    protected $signature = 'fix:opd';
    protected $description = 'Fix orphaned OPD data';

    public function handle()
    {
        $masters = MasterOpd::pluck('nama')->toArray();
        $permintaans = PermintaanOpd::pluck('opd')->unique()->toArray();
        
        $orphans = array_diff($permintaans, $masters);
        
        foreach ($orphans as $orphan) {
            // Find the closest match in MasterOpd using similar_text
            $bestMatch = null;
            $bestScore = 0;
            foreach ($masters as $master) {
                similar_text($orphan, $master, $percent);
                if ($percent > $bestScore) {
                    $bestScore = $percent;
                    $bestMatch = $master;
                }
            }
            
            $this->info("Orphan: $orphan => Best Match: $bestMatch (Score: $bestScore)");
            
            if ($bestScore > 80 && $bestMatch) {
                $target = $bestMatch;
            } else if (str_contains($orphan, 'PERHUBUNGAN') && in_array('DINAS PERHUBUNGAN', $masters)) {
                $target = 'DINAS PERHUBUNGAN';
            } else {
                continue;
            }

            $records = PermintaanOpd::where('opd', $orphan)->get();
            foreach ($records as $record) {
                try {
                    $record->update(['opd' => $target]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // Duplicate exists! Merge documents
                    $existing = PermintaanOpd::where('permintaan_id', $record->permintaan_id)
                        ->where('opd', $target)->first();
                    if ($existing) {
                        \App\Models\Dokumen::where('permintaan_opd_id', $record->id)
                            ->update(['permintaan_opd_id' => $existing->id]);
                        $record->delete();
                    }
                }
            }
            $this->info("  -> FIXED $orphan to $target");
        }
        $this->info("Done");
    }
}
