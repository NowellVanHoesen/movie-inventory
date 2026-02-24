<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    cast: {
        type: Array,
        required: true,
    },
    display_limit: {
        type: Number,
        default: 20,
    },
    multi_cols: {
        type: Boolean,
        default: false,
    },
});

const showAll = ref(false);

const castToShow = computed(() => {
    if (!props.cast) {
        return [];
    }

    let cast_members = props.cast.filter((cast_member) => !cast_member.pivot.character.toLowerCase().includes("uncredited"));

    if (cast_members.length <= props.display_limit) {
        return cast_members;
    }

    return showAll.value ? cast_members : cast_members.slice(0, props.display_limit);
});
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h2 class="text-2xl">Cast Members</h2>
            <button
                v-if="props.cast?.length > display_limit"
                @click.prevent="showAll = !showAll"
                class="bg-cold-steel-600 hover:bg-cold-steel-700 focus:bg-cold-steel-700 cursor-pointer rounded-md border border-transparent px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
            >
                {{ showAll ? "Show Less" : "Show All" }}
            </button>
        </div>
        <ul :class="{ 'columns-1 gap-4 lg:columns-2': multi_cols }" class="mt-4">
            <li class="flex w-full" v-for="cast_member in castToShow" :key="cast_member.slug">
                <Link :href="route('castMember.show', cast_member)" class="text-indigo-600">{{ cast_member.name }}</Link>
                <span
                    class="flex flex-1 text-right before:mx-1 before:mb-[0.3rem] before:flex-1 before:border-b-2 before:border-dotted before:border-b-gray-500 before:content-['']"
                    >{{ cast_member.pivot.character }}</span
                >
            </li>
        </ul>
    </div>
</template>
