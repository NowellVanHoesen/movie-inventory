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
    // When expanded, keep the list at its collapsed height and scroll it instead of
    // letting it grow. Used inside the movie modal so "Show All" doesn't stretch the
    // modal to full screen height.
    contain_expanded: {
        type: Boolean,
        default: false,
    },
});

const showAll = ref(false);

const listWrapper = ref(null);

// The collapsed list's rendered height, captured just before expanding.
const lockedHeight = ref(null);

const toggleShowAll = () => {
    if (!showAll.value && props.contain_expanded && listWrapper.value) {
        lockedHeight.value = listWrapper.value.offsetHeight;
    }

    showAll.value = !showAll.value;

    if (!showAll.value) {
        lockedHeight.value = null;
        listWrapper.value?.scrollTo({ top: 0 });
    }
};

const dispShowAllBtn = ref(false);

const castToShow = computed(() => {
    if (!props.cast) {
        return [];
    }

    let cast_members = props.cast.filter(
        (cast_member) => !(cast_member.pivot?.character ?? "").toLowerCase().includes("uncredited"),
    );

    if (cast_members.length <= props.display_limit) {
		dispShowAllBtn.value = false;
        return cast_members;
    }

    dispShowAllBtn.value = true;

    return showAll.value ? cast_members : cast_members.slice(0, props.display_limit);
});
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h2 class="text-2xl py-1">Cast Members</h2>
            <button
                v-if="dispShowAllBtn"
                dusk="cast-show-all-btn"
                @click.prevent="toggleShowAll"
                class="bg-cold-steel-600 hover:bg-cold-steel-700 focus:bg-cold-steel-700 cursor-pointer rounded-md border border-transparent px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
            >
                {{ showAll ? "Show Less" : "Show All" }}
            </button>
        </div>
        <!--
            The scroll container wraps the <ul> rather than being it: a height-capped
            element with CSS columns overflows sideways into extra columns instead of
            scrolling down. overflow-y-auto only shows a scrollbar when the list overflows.
        -->
        <div
            ref="listWrapper"
            dusk="cast-list"
            class="mt-4"
            :class="{ 'overflow-y-auto overscroll-contain pr-2 [scrollbar-width:thin]': lockedHeight }"
            :style="lockedHeight ? { maxHeight: `${lockedHeight}px` } : null"
        >
            <ul :class="{ 'columns-1 gap-4 lg:columns-2': multi_cols }">
                <li class="flex w-full" v-for="cast_member in castToShow" :key="cast_member.slug">
                    <Link :href="route('castMember', cast_member)" class="text-indigo-600">{{ cast_member.name }}</Link>
                    <span
                        class="flex flex-1 text-right before:mx-1 before:mb-[0.3rem] before:flex-1 before:border-b-2 before:border-dotted before:border-b-gray-500 before:content-['']"
                        >{{ cast_member.pivot?.character }}</span
                    >
                </li>
            </ul>
        </div>
    </div>
</template>
