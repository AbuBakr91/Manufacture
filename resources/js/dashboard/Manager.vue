<template>
    <div class="wrapper">
        <dashboard-sidebar></dashboard-sidebar>

        <div class="main">
            <dashboard-header></dashboard-header>
            <router-view></router-view>
        </div>
    </div>

</template>

<script>
import DashboardHeader from "./components/DashboardHeader"
import DashboardSidebar from "./components/DashboardSidebar";
import store from "../store";

export default {
  data() {
    return {
      role: store.state.auth.role
    }
  },
  mounted() {
    if(!(this.role === 'manager')) {
      this.$router.push('/')
    }
  },
  components: {DashboardHeader, DashboardSidebar}
}
</script>

<style scoped>

</style>
