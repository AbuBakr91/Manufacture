<template>
    <div class="wrapper">
        <dashboard-sidebar></dashboard-sidebar>

        <div class="main">
            <dashboard-header></dashboard-header>
            <router-view></router-view>
            <dashboard-footer></dashboard-footer>
        </div>
    </div>

</template>

<script>
import DashboardHeader from "../components/DashboardHeader"
import DashboardFooter from "../components/DashboardFooter";
import DashboardSidebar from "../components/DashboardSidebar";
export default {
    components: {DashboardHeader, DashboardFooter, DashboardSidebar}
}
</script>

<style scoped>

</style>
