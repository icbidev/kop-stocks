<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, EggFried, ChartNoAxesCombined, Truck, Box, Book, Weight, ChartBarStacked,PackageSearch,FileStack } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Products',
        href: '/products',
        icon: EggFried,
    },
        {
        title: 'Categories',
        href: '/categories',
        icon: ChartBarStacked,
    },

            {
        title: 'Suppliers',
        href: '/suppliers',
        icon: PackageSearch,
    },


                {
        title: 'Inventory',
        href: '/inventory',
        icon: Box,
    },
        {
        title: 'Stocks',
        href: '/low-stocks',
        icon: Book,
    },

];

const footerNavItems: NavItem[] = [
            {
        title: 'Audit Logs',
        href: '/audit-logs',
        icon: FileStack,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
