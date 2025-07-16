{{-- helloword.blade.php --}}
<section class="container">
    <h1>HelloWorld Function</h1>

    <form id="helloWorldForm" method="GET">
        <div class="form-group">
            <label for="n">Masukkan nilai n:</label>
            <input type="number" id="n" name="n" value="{{ old('n', session('n')) }}" min="1" max="100" required placeholder="Masukkan nilai lebih dari atau sama dengan 1">
        </div>
        <button type="submit">Tampilkan</button>
    </form>

    <div id="resultContainer">
        @if(session('show_result') && session('sequences') && count(session('sequences')) > 0)
            <h2>helloworld({{ session('n') }}) => {{ implode(' ', session('sequences')) }}</h2>
        @endif
    </div>
</section>

<script>
    $(document).ready(function() {
        $('#helloWorldForm').on('submit', function(e) {
            e.preventDefault();

            var n = $('#n').val().trim();

            n = n.replace(/^0+/, '');

            if (!n || n < 1) {
                alert('Masukkan nilai n yang valid (minimal 1)');
                return;
            }

            $('#n').val(n);

            $.ajax({
                url: '{{ route("helloworld") }}',
                type: 'GET',
                data: { n: n },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if(response.sequences && response.sequences.length > 0) {
                        var sequenceString = response.sequences.join(' ');
                        var resultHTML = '<h2>helloworld(' + n + ') => ' + sequenceString + '</h2>';
                        $('#resultContainer').html(resultHTML);
                    } else {
                        $('#resultContainer').html('<h2>Tidak ada hasil</h2>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error details:', xhr.responseText);
                    alert('Terjadi kesalahan. Coba lagi.');
                }
            });
        });
    });
</script>
