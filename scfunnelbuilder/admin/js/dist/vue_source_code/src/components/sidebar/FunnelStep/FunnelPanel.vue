<script setup>
import { ref } from 'vue';
import SidebarHeader from '../SidebarHeader.vue';
import Sidebar from '../Sidebar.vue';
import { useNodeStore } from '@/store/nodes';
import BaseTabPanel from '@/components/tab-panel/BaseTabPanel.vue';
import { checkForStepType } from '@/utils/Common';
const nodeStore = useNodeStore();

const props = defineProps({
    openModal: Boolean
})



</script>
<template>
    <!-- nodeStore.getActiveStep.slug_type !== 'percentage_split' -->
    <Sidebar
        :class="[nodeStore.modalStatus && checkForStepType(nodeStore.getActiveStep.slug_type, ['percentage_split', 'conditional_split'], true) ? 'open' : '']">
        <template v-slot:s-header>
            <SidebarHeader :nodeData="nodeStore.getActiveStep"> <template #header>
                    <h4 class="title">Add {{ nodeStore.getActiveStep.title }}</h4>
                </template>
            </SidebarHeader>
        </template>
        <template v-slot:s-body>
            <div class="sidebar_body_container mb-4">
                <div class="tab_body_section">
                    <BaseTabPanel :data="nodeStore.getActiveStep" />
                </div>

            </div>
        </template>
    </Sidebar>
</template>
<style lang="scss">
.hr_tabs_container {
    display: flex;
    align-items: center;
}

.tab_body_section {
    width: 100%;
}
</style>