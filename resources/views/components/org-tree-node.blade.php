@props(['node', 'allNodes'])

@php
    $children = collect($allNodes)->where('parent_id', $node->id)->whereIn('jenis_hubungan', ['struktural'])->sortBy('urutan');
    // Backward compat: 'asisten' (lama) dianggap kanan
    $assistantsLeft = collect($allNodes)->where('parent_id', $node->id)->where('jenis_hubungan', 'asisten_kiri')->sortBy('urutan');
    $assistantsRight = collect($allNodes)->where('parent_id', $node->id)->whereIn('jenis_hubungan', ['asisten_kanan', 'asisten'])->sortBy('urutan');
    
    $marginLeft = $assistantsLeft->count() > 0 ? 'margin-left: 210px;' : '';
    $marginRight = $assistantsRight->count() > 0 ? 'margin-right: 210px;' : '';
    $isRoot = $node->parent_id == null;
@endphp

<li style="{{ $marginLeft }} {{ $marginRight }}">
    <div class="org-node-wrapper">
        {{-- ===== LEFT ASSISTANTS ===== --}}
        @if($assistantsLeft->count() > 0)
        <div class="assistant-branch assistant-left">
            <div class="assistant-cards">
                @foreach($assistantsLeft as $asisten)
                <div class="assistant-card">
                    <span class="assistant-badge">Staf Khusus</span>
                    <div class="assistant-inner">
                        <img class="assistant-avatar" 
                             src="{{ $asisten->foto_pejabat ? asset('storage/'.$asisten->foto_pejabat) : 'https://ui-avatars.com/api/?name='.urlencode($asisten->nama_pejabat ?? $asisten->jabatan).'&background=f59e0b&color=fff&size=64' }}" 
                             alt="{{ $asisten->jabatan }}">
                        <div class="assistant-info">
                            <h4>{{ $asisten->jabatan }}</h4>
                            <p>{{ $asisten->nama_pejabat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="assistant-line"></div>
        </div>
        @endif

        {{-- ===== MAIN CARD ===== --}}
        <div class="org-card {{ $isRoot ? 'org-card-root' : '' }}">
            <div class="org-card-accent"></div>
            <div class="org-card-body">
                <div class="org-avatar-ring">
                    <img src="{{ $node->foto_pejabat ? Storage::url($node->foto_pejabat) : 'https://ui-avatars.com/api/?name='.urlencode($node->nama_pejabat ?? $node->jabatan).'&background=3b82f6&color=fff&size=128' }}" 
                         alt="{{ $node->jabatan }}">
                </div>
                <h3 class="org-title">{{ $node->jabatan }}</h3>
                <p class="org-name">{{ $node->nama_pejabat ?? '-' }}</p>
            </div>
        </div>

        {{-- ===== RIGHT ASSISTANTS ===== --}}
        @if($assistantsRight->count() > 0)
        <div class="assistant-branch assistant-right">
            <div class="assistant-line"></div>
            <div class="assistant-cards">
                @foreach($assistantsRight as $asisten)
                <div class="assistant-card">
                    <span class="assistant-badge">Staf Khusus</span>
                    <div class="assistant-inner">
                        <img class="assistant-avatar" 
                             src="{{ $asisten->foto_pejabat ? asset('storage/'.$asisten->foto_pejabat) : 'https://ui-avatars.com/api/?name='.urlencode($asisten->nama_pejabat ?? $asisten->jabatan).'&background=f59e0b&color=fff&size=64' }}" 
                             alt="{{ $asisten->jabatan }}">
                        <div class="assistant-info">
                            <h4>{{ $asisten->jabatan }}</h4>
                            <p>{{ $asisten->nama_pejabat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ===== RECURSIVE CHILDREN ===== --}}
    @if($children->count() > 0)
    <ul>
        @foreach($children as $child)
            <x-org-tree-node :node="$child" :allNodes="$allNodes" />
        @endforeach
    </ul>
    @endif
</li>
