<script setup>

import OptionPanel from '@/components/Option/OptionPanel.vue';
import Navbar from '@/components/header/Navbar.vue';
import FunnelPanel from '@/components/sidebar/FunnelStep/FunnelPanel.vue';
import { useNodeStore } from '@/store/nodes';
import ConditionalSplit from '@/components/sidebar/ConditionalSplit.vue';
import PercentageSplit from '@/components/sidebar/PercentageSplit.vue';
import { getLastNode, checkForStepType, isObjectEmpty } from '@/utils/Common'
import { onUpdated } from 'vue';
const nodeStore = useNodeStore();
// const props = defineProps({
//     nodes: Array
// })

</script>
<template>
    <div class="main_layout_wrapper">
        <header>
            <Navbar />
        </header>
        <!--  v-if="checkForStepType(getLastNode()?.data?.slug_type, ['conditional_split', 'percentage_split'], true)" -->
        <div class="main_content_wrapper">
            <FunnelPanel class="funnel_panel"
                v-if="checkForStepType(getLastNode()?.data?.slug_type, ['conditional_split', 'percentage_split'], true)" />
            <ConditionalSplit class="funnel_panel" />
            <!-- v-if="getLastNode()?.data?.slug_type === 'conditional_split'"  -->
            <!-- v-if="getLastNode()?.data?.slug_type === 'conditional_split'" -->
            <!-- v-if="getLastNode()?.data?.slug_type === 'percentage_split'" -->
            <PercentageSplit />
            <OptionPanel v-if="!isObjectEmpty(nodeStore.getNodeClickedData)" />
        </div>
    </div>
</template>
<style lang="scss">
header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    padding: 15px 4px;
    background: #FFFFFF;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
    z-index: 99;
}
</style>