<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link,usePage  } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, EggFried, ChartNoAxesCombined, Truck, Box, Book, Weight, ChartBarStacked,PackageSearch,FileStack,Users,Star } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
const page = usePage()
const user = page.props.auth.user
const allowedRoutes =  page.props.auth.allowedRoutes
// Filter nav items based on route permission
const iconMap = {
  'dashboard': LayoutGrid,
  'users.index': Users,
  'roles.index': Star,
  'products': EggFried,
  'categories.index': ChartBarStacked,
  'suppliers.index': PackageSearch,
  'inventory.index': Box,
  'low-stocks.index': Book,
  'audit.logs': FileStack,
}

const titleMap = {
  'dashboard': 'Dashboard',
  'users.index': 'Users',
  'roles.index': 'Roles',
  'products': 'Products',
  'categories.index': 'Categories',
  'suppliers.index': 'Suppliers',
  'inventory.index': 'Inventory',
  'low-stocks.index': 'Stocks',
  'audit.logs': 'Audit Logs',
}

const mainNavItems = []
const footerNavItems = []

allowedRoutes.forEach(item => {
  const fullRoute = item

  if (!fullRoute || typeof fullRoute !== 'string') return

  const [role, ...rest] = fullRoute.split('.')
  const routeKey = rest.join('.')

  if (role !== user.name || !iconMap[routeKey]) return

  const navItem = {
    title: titleMap[routeKey],
    href: route(fullRoute),
    icon: iconMap[routeKey],
  }
    console.log(routeKey);
  if (routeKey === 'audit.logs') {
    footerNavItems.push(navItem)
  } else {
    mainNavItems.push(navItem)
  }
})
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
<Link :href="route(allowedRoutes.includes(`${user.name}.dashboard`) ? `${user.name}.dashboard` : 'cashier.inventory.index')">
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
