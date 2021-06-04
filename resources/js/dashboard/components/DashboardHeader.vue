<template>
    <nav class="navbar navbar-expand navbar-light navbar-bg">
        <a class="sidebar-toggle js-sidebar-toggle">
            <i class="hamburger align-self-center"></i>
        </a>

        <div class="navbar-collapse collapse">
            <ul class="navbar-nav navbar-align">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                        {{user.firstname}} {{user.lastname}}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="pages-profile.html"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" @click.prevent="logout">Log out</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
import store from "../../store"
import {useRouter} from 'vue-router'
import {useStore} from 'vuex'

export default {
    data() {
        return {
            user: JSON.parse(store.state.auth.user),
        }
    },
    setup() {
      const router = useRouter()
      const store = useStore()

      return {
        logout: () => {
          store.commit('auth/logout')
          router.push('/login')
        }
      }
    }
}
</script>

<style scoped>

</style>
