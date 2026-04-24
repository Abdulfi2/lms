<div x-data="{ categories: [] }" x-init="fetch('/api/categories').then(r => r.json()).then(d => categories = d)" class="space-y-4">
    <template x-for="cat in categories" :key="cat.id">
        <div>
            <div class="flex items-center justify-between p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-2">
                    <i :class="cat.icon" :style="{ color: cat.color }"></i>
                    <a :href="'/courses?category=' + cat.slug" class="font-medium" x-text="cat.name"></a>
                    <span class="text-xs text-gray-500" x-text="cat.courses_count + ' kursus'"></span>
                </div>
                <button x-show="cat.children.length > 0" @click="cat.showChildren = !cat.showChildren"
                    class="text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div x-show="cat.showChildren" x-collapse class="ml-6 mt-1 space-y-1">
                <template x-for="child in cat.children" :key="child.id">
                    <a :href="'/courses?category=' + child.slug"
                        class="block text-sm text-gray-600 dark:text-gray-400 hover:text-primary"
                        x-text="child.name"></a>
                </template>
            </div>
        </div>
    </template>
</div>
