<?php
\App\Models\User::where('id', 1)->update(['role' => 'Admin']);
\App\Models\User::whereRaw('LOWER(name) LIKE ?', ['%super admin%'])->update(['role' => 'Admin']);
echo "DONE\n";
