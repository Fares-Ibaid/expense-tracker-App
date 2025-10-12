<script setup>
import { ref } from 'vue';
/*import DashboardPage from './DashboardPage.vue';*/
import DashboardSection from "@/components/dashboard/DashboardSection.vue";
import Navbar from "@/components/Navbar.vue";
import SettingsPanel from "@/components/SettingsPanel.vue";
import LoadingOverlay from "@/components/utilities/LoadingOverlay.vue";


const currentView = ref('dashboardsection');
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
           <DashboardSection v-if="currentView === 'dashboardsection'"/>
           <SettingsPanel v-if="currentView === 'settings'"/>
        </main>
    </div>
</template>
