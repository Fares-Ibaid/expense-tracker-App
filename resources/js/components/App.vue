<script setup>
import { ref } from 'vue';
/*import DashboardPage from './DashboardPage.vue';*/
import DashboardNew from "@/components/DashboardNew.vue";
import Navbar from "@/components/Navbar.vue";
import SettingsPanel from "@/components/SettingsPanel.vue";
import LoadingOverlay from "@/components/utilities/LoadingOverlay.vue";


const currentView = ref('dashboardnew');
const isLoading = ref(false);

const switchView = (view) => {
    isLoading.value = true;
  setTimeout(() => {
        currentView.value = view;
        isLoading.value = false;
    }, 500); // Simulate a short loading time

};
</script>


<template>
    <div>
        <header class="mb-8">
        </header>
        <main>
            <loading-overlay
            :isLoading="isLoading"
            />
           <navbar
               @navigate="switchView"
               :current-view="currentView"
           />
            <!-- Based on the active tab will the corresponding component get rendered -->
           <DashboardNew v-if="currentView === 'dashboardnew'"/>
           <SettingsPanel v-if="currentView === 'settings'"/>
        </main>
    </div>
</template>
