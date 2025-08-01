<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Semantic Search</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        .search-container {
            max-width: 700px;
            margin: 80px auto;
            padding: 40px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        #searchInput:focus {
            box-shadow: none;
            border-color: #86b7fe;
            outline: none;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .badge-score {
            font-size: 0.85rem;
            background-color: #2f855a;
            color: white;
            border-radius: 20px;
            padding: 4px 12px;
        }

        .btn-outline-primary {
            border: 1px solid #3182ce;
            background-color: white;
            color: #3182ce;
            padding: 8px 16px;
        }

        .badge-score {
            font-size: 0.70rem;
            background-color: #2f855a;
            color: white;
            border-radius: 20px;
            padding: 4px 12px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="search-container">
            <h2 class="mb-4 text-center">AI Vector Search</h2>

            <form id="searchForm" class="mb-4">
                <div class="input-group">
                    <input type="text" name="query" id="searchInput" class="form-control"
                        placeholder="Search category or service..." required>
                    <button class="btn btn-primary" type="submit" id="searchBtn" disabled>Search</button>
                </div>
            </form>

            <div id="searchResults"></div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {

            $('#searchInput').on('input', function () {
                $('#searchBtn').prop('disabled', $(this).val().trim() === '');
            });

            $('#searchForm').on('submit', function (e) {
                e.preventDefault();
                const query = $('#searchInput').val();

                $('#searchResults').html(`
                    <div class="text-center my-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Searching, please wait...</p>
                    </div>
                `);

                $.ajax({
                    url: "{{ route('vector.search') }}",
                    type: 'GET',
                    data: { query: query },
                    success: function (response) {
                        if (response.success) {
                            $('#searchResults').html(response.content);
                        } else {
                            $('#searchResults').html(`<div class="alert alert-warning">${response.message}</div>`);
                        }
                    },
                    error: function () {
                        $('#searchResults').html('<div class="alert alert-danger">An error occurred while searching.</div>');
                    }
                });
            });
        });
    </script>

</body>

</html>