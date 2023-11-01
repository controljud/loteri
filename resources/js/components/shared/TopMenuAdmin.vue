<template>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 260px;">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <b-icon icon="bootstrap-fill"></b-icon>
            <span class="fs-4">Admin</span>
        </a>

        <hr />

        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <router-link :to="{name: 'admin'}" class="nav-link link-dark" exact>
                    <b-icon icon="graph-up"></b-icon>
                    Dashboard
                </router-link>
            </li>
            <li>
                <router-link :to="{name: 'admin.sorteios'}" class="nav-link link-dark" exact>
                    <b-icon icon="star"></b-icon>
                    Sorteios
                </router-link>
            </li>
            <li>
                <router-link :to="{name: 'admin.jogos'}" class="nav-link link-dark" exact>
                    <b-icon icon="bank"></b-icon>
                    Jogos
                </router-link>
            </li>
            <li>
                <router-link :to="{name: 'admin.usuarios'}" class="nav-link link-dark" exact>
                    <b-icon icon="person-circle"></b-icon>
                    Usuários
                </router-link>
            </li>
        </ul>

        <hr />

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img :src="usuario_image" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong>{{usuario.name}}</strong>
            </a>
            <ul class="dropdown-menu text-small shadow">
                <li>
                    <router-link :to="{name: 'admin.perfil'}" class="dropdown-item" exact>
                        <b-icon icon="person-circle"></b-icon>
                        Perfil
                    </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="javascript:void(0)" @click="sair"><font-awesome-icon icon="fa-solid fa-close" /> Sair</a></li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    components: {
        
    },

    data() {
        return {
            usuario: null,
            usuario_image: '/images/user-admin.png'
        }
    },

    created: function() {
        this.usuario = JSON.parse(localStorage.getItem('user'));
        this.usuario_image = this.usuario.imagem;
    },

    methods: {
        sair() {
            localStorage.removeItem('token');
            window.location.href = "/";
        },
    }
}
</script>

<style scoped>
.nav-link:hover {
    background-color: #cdcdcd;
}

.b-icon {
    margin-right: 20px;
}
</style>