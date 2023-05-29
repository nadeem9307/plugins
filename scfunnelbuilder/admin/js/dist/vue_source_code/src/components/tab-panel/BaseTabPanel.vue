<script setup>
import BaseTab from "@/components/tab-panel/BaseTab.vue";
import CreateNew from "@/components/sidebar/FunnelStep/CreateNew.vue";
import UseExisting from "@/components/sidebar/FunnelStep/UseExisting.vue";
import Default from "@/components/sidebar/FunnelStep/Default.vue";
import { ref } from "vue";
import BaseTabContent from "@/components/tab-panel/BaseTabContent.vue";
import BaseButton from "../buttons/BaseButton.vue";
import { useNodeStore } from '@/store/nodes';
import { getStepTemplates } from "@/api/funnel/funnel-service"
const nodeStore = useNodeStore();
const activeTab = ref(null);
const toggleTab = async (tabId, contentPage) => {
    activeTab.value = tabId;
    if (contentPage !== 'Create new') return
    await getStepTemplates(nodeStore.getActiveStep.slug_type);

};
const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})
const horiTabs = [
    {
        name: "Default",
        content: Default,
    },
    {
        name: "Use existing",
        content: UseExisting,
    },
    {
        name: "Create new",
        content: CreateNew,
    },
];
const tabs = [

    {
        name: "Use existing",
        content: UseExisting,
    },
    {
        name: "Create new",
        content: CreateNew,
    },
];
const checkNodeType = (node_slug) => {
    const slugArray = ['opt_in', 'checkout']
    return slugArray.includes(node_slug) ? horiTabs : tabs;
}

</script>
<template>
    <div class="inp_actions">
        <div class="hr_tabs_container">
            <h4 class="sub_title pe-3">Design</h4>
            <div class="tabs_wrapper">
                <!-- class="hr_tabs_k" -->
                <BaseTab :tabOptions="checkNodeType(data.slug_type)" @getActiveTabId="toggleTab" />
            </div>
        </div>

        <div class="mt-3">
            <BaseTabContent v-for="(tab, index) in checkNodeType(data.slug_type)" :key="index"
                :activeContent="activeTab === index">
                <component :is="tab.content" />
            </BaseTabContent>
        </div>
    </div>
</template>
<style lang="scss">
.hr_tabs_container {
    width: 100%;

    h4 {
        width: 20%;
    }

    .tabs_wrapper {
        display: flex;
        align-items: center;
        width: auto;
        flex: 0 1 auto;
    }
}

.inp_actions {
    width: 100%;
}
</style>
