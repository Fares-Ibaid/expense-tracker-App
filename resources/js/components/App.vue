<script setup>
import { ref } from 'vue';
/*import DashboardPage from './DashboardPage.vue';*/
import DashboardSection from "@/components/dashboard/DashboardSection.vue";
import ExpensesSection from "@/components/expenses/ExpensesSection.vue";
import Navbar from "@/components/layout/Navbar.vue";
import SettingsPanel from "@/components/settings/SettingsPanel.vue";
import LoadingOverlay from "@/components/utilities/LoadingOverlay.vue";
import ReportSection     from "@/components/Report/ReportSection.vue";


const currentView = ref('dashboard');
const isLoading = ref(false);
const key = ref(null); // Define the "key" property

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
           <DashboardSection v-if="currentView === 'dashboard'"/>
            <ExpensesSection
                :key="key" @update:key="key = $event"
                v-if="currentView === 'expenses'"/>
           <SettingsPanel
               :key="key" @update:key="key = $event"
               v-if="currentView === 'settings'"/>
           <ReportSection v-if="currentView === 'reports'"/>
        </main>
    </div>
</template>
