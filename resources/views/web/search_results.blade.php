@if($results->isEmpty())
    <div class="alert alert-warning text-center">No results found.</div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($results as $result)


            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <h5 class="card-title text-primary mb-2">
                                <i class="bi bi-folder-fill me-2"></i>{{ $result['category'] }}
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <i class="bi bi-diagram-3-fill me-2"></i>{{ $result['sub_category'] }}
                            </h6>
                            <p class="mb-0">
                                <i class="bi bi-tools me-2 text-secondary"></i>
                                <strong>Service:</strong> {{  $result['service'] ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="mt-auto">
                            <span class="badge bg-success px-3 py-2 rounded-pill badge-score">
                                Similarity Score: {{number_format($result['score'] * 100, 2) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
    </div>
@endif