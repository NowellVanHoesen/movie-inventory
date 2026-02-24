<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { Form, Link } from "@inertiajs/vue3";
</script>

<template>
    <Menu as="div" class="relative inline-block">
        <MenuButton class="group mx-3 my-2 inline-flex items-center rounded-full p-0.5 ring-2 ring-transparent">
            <div class="ms-1">
                <img
                    class="size-10 rounded-full border-transparent ring-2 ring-transparent group-hover:ring-blue-300 group-focus:ring-blue-300"
                    :src="$page.props.auth.user.gravatar_url"
                    alt=""
                />
            </div>
        </MenuButton>

        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <MenuItems
                class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-gray-800 outline-1 -outline-offset-1 outline-white/10"
            >
                <div class="py-1">
                    <MenuItem v-slot="{ active = route().current('dashboard') }">
                        <Link
                            :href="route('dashboard')"
                            :class="[
                                active ? 'bg-white/5 text-white outline-hidden' : 'text-gray-300',
                                'block px-4 py-2 text-sm',
                            ]"
                        >
                            Dashboard
                        </Link>
                    </MenuItem>
                    <MenuItem v-slot="{ active = route().current('profile.edit') }">
                        <Link
                            :href="route('profile.edit')"
                            :class="[
                                active ? 'bg-white/5 text-white outline-hidden' : 'text-gray-300',
                                'block px-4 py-2 text-sm',
                            ]"
                        >
                            Profile
                        </Link>
                    </MenuItem>
                    <Form method="POST" :action="route('logout')">
                        <MenuItem v-slot="{ active = false }">
                            <button
                                type="submit"
                                :class="[
                                    active ? 'bg-white/5 text-white outline-hidden' : 'text-gray-300',
                                    'block w-full px-4 py-2 text-left text-sm',
                                ]"
                            >
                                Log out
                            </button>
                        </MenuItem>
                    </Form>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
