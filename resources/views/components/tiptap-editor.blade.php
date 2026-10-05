@props(['name' => 'content', 'content' => '', 'placeholder' => 'Tulis di sini...', 'minHeight' => '250px'])

@once
    @push('scripts')
        <script type="module">
            import { Editor } from 'https://cdn.jsdelivr.net/npm/@tiptap/core@2/+esm';
            import StarterKit from 'https://cdn.jsdelivr.net/npm/@tiptap/starter-kit@2/+esm';
            import Underline from 'https://cdn.jsdelivr.net/npm/@tiptap/extension-underline@2/+esm';
            import Link from 'https://cdn.jsdelivr.net/npm/@tiptap/extension-link@2/+esm';
            import Placeholder from 'https://cdn.jsdelivr.net/npm/@tiptap/extension-placeholder@2/+esm';

            window.Tiptap = { Editor, StarterKit, Underline, Link, Placeholder };
            window.dispatchEvent(new Event('tiptap:loaded'));
        </script>
        <script>
            function tiptapEditor(initialContent, placeholderText) {
                return {
                    editor: null,
                    tick: 0,
                    init(editorEl, hiddenInput) {
                        hiddenInput.value = initialContent;

                        const build = () => {
                            const T = window.Tiptap;
                            this.editor = new T.Editor({
                                element: editorEl,
                                extensions: [
                                    T.StarterKit.configure({ heading: { levels: [2, 3] } }),
                                    T.Underline,
                                    T.Link.configure({ openOnClick: false, autolink: true }),
                                    T.Placeholder.configure({ placeholder: placeholderText }),
                                ],
                                content: initialContent,
                                editorProps: {
                                    attributes: {
                                        class: 'prose prose-sm dark:prose-invert max-w-none focus:outline-none',
                                    },
                                },
                                onUpdate: ({ editor }) => {
                                    hiddenInput.value = editor.getHTML();
                                },
                                onTransaction: () => {
                                    this.tick++;
                                },
                            });
                        };

                        if (window.Tiptap) {
                            build();
                        } else {
                            window.addEventListener('tiptap:loaded', build, { once: true });
                        }
                    },
                    is(mark, attrs) {
                        this.tick;
                        return this.editor ? this.editor.isActive(mark, attrs) : false;
                    },
                    setLink() {
                        const previousUrl = this.editor.getAttributes('link').href;
                        const url = window.prompt('Masukkan URL tautan:', previousUrl || 'https://');
                        if (url === null) return;
                        if (url === '') {
                            this.editor.chain().focus().extendMarkRange('link').unsetLink().run();
                            return;
                        }
                        this.editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
                    },
                };
            }
        </script>
    @endpush
@endonce

<div x-data="tiptapEditor(@js($content), @js($placeholder))" x-init="init($refs.editorEl, $refs.hiddenInput)">
    <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
        <div class="flex flex-wrap items-center gap-1 p-2 border-b border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
            <template x-if="editor">
                <div class="flex flex-wrap items-center gap-1">
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                        :class="is('heading', { level: 2 }) ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2 py-1 rounded text-xs font-semibold">H2</button>
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                        :class="is('heading', { level: 3 }) ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2 py-1 rounded text-xs font-semibold">H3</button>

                    <span class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></span>

                    <button type="button" @click="editor.chain().focus().toggleBold().run()"
                        :class="is('bold') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2.5 py-1 rounded text-sm font-bold">B</button>
                    <button type="button" @click="editor.chain().focus().toggleItalic().run()"
                        :class="is('italic') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2.5 py-1 rounded text-sm italic">I</button>
                    <button type="button" @click="editor.chain().focus().toggleUnderline().run()"
                        :class="is('underline') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2.5 py-1 rounded text-sm underline">U</button>
                    <button type="button" @click="editor.chain().focus().toggleStrike().run()"
                        :class="is('strike') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2.5 py-1 rounded text-sm line-through">S</button>

                    <span class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></span>

                    <button type="button" @click="editor.chain().focus().toggleBulletList().run()"
                        :class="is('bulletList') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2 py-1 rounded text-xs" title="Daftar bullet">&bull; List</button>
                    <button type="button" @click="editor.chain().focus().toggleOrderedList().run()"
                        :class="is('orderedList') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2 py-1 rounded text-xs" title="Daftar bernomor">1. List</button>
                    <button type="button" @click="editor.chain().focus().toggleBlockquote().run()"
                        :class="is('blockquote') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2 py-1 rounded text-sm" title="Kutipan">&ldquo;&rdquo;</button>
                    <button type="button" @click="setLink()"
                        :class="is('link') ? 'bg-primary/10 text-primary' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2 py-1 rounded text-xs" title="Tautan">Link</button>

                    <span class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></span>

                    <button type="button" @click="editor.chain().focus().unsetAllMarks().clearNodes().run()"
                        class="px-2 py-1 rounded text-xs text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700" title="Bersihkan format">Clear</button>
                </div>
            </template>
            <template x-if="!editor">
                <span class="text-xs text-gray-400">Memuat editor...</span>
            </template>
        </div>
        <div x-ref="editorEl" class="bg-white dark:bg-gray-900 px-3 py-2 overflow-y-auto" style="min-height: {{ $minHeight }};"></div>
    </div>
    <textarea name="{{ $name }}" x-ref="hiddenInput" class="hidden">{{ $content }}</textarea>
</div>
