@props(['name' => 'content', 'content' => '', 'placeholder' => 'Tulis di sini...', 'minHeight' => 300, 'uploadUrl' => null])

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
    @endpush
@endonce

@php($editorId = 'tinymce-' . \Illuminate\Support\Str::random(8))

<textarea id="{{ $editorId }}" name="{{ $name }}">{{ $content }}</textarea>

@push('scripts')
<script>
    (function () {
        @if ($uploadUrl)
        function uploadImageFile(file) {
            return new Promise(function (resolve, reject) {
                const formData = new FormData();
                formData.append('image', file, file.name);

                fetch(@js($uploadUrl), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (result.ok && result.data.location) {
                            resolve(result.data.location);
                        } else {
                            reject(result.data.message || 'Gagal mengunggah gambar.');
                        }
                    })
                    .catch(function () {
                        reject('Gagal mengunggah gambar. Silakan coba lagi.');
                    });
            });
        }
        @endif

        tinymce.init({
            selector: '#{{ $editorId }}',
            height: {{ (int) $minHeight }},
            menubar: false,
            branding: false,
            promotion: false,
            // TinyMCE default-nya menyimpan src gambar/link sebagai path RELATIF terhadap
            // halaman saat ini (mis. "../../storage/..."), yang jadi salah begitu konten
            // ini dirender di halaman lain dengan kedalaman URL berbeda. Paksa tetap apa
            // adanya (root-relative "/storage/..." dari endpoint upload).
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            placeholder: @js($placeholder),
            plugins: 'lists link table code fullscreen autoresize{{ $uploadUrl ? ' image' : '' }}',
            toolbar: 'blocks | bold italic underline strikethrough | bullist numlist | blockquote link{{ $uploadUrl ? ' image' : '' }} table | removeformat code fullscreen',
            @if ($uploadUrl)
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype !== 'image') return;

                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    uploadImageFile(file)
                        .then(function (url) {
                            callback(url, { alt: file.name });
                        })
                        .catch(function (message) {
                            if (window.toast) {
                                window.toast.error(message);
                            } else {
                                alert(message);
                            }
                        });
                });
                input.click();
            },
            images_upload_handler: function (blobInfo) {
                return uploadImageFile(blobInfo.blob());
            },
            @endif
        });
    })();
</script>
@endpush
