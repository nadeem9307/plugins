<script  setup>
import { ref } from 'vue';
import placehoderImg from "@/assets/img/img_placeholder.svg";
import Icon from './Icons/Icon.vue';
import { useNodeStore } from "@/store/nodes";
import { randomId, getLastNode } from '@/utils/Common';
const nodeStore = useNodeStore()
const props = defineProps({
    data: {
        type: Object
    }
})
// on right steps sidebar node click left sidebar will not be visible
const openSidebarFunnel = (data) => {
    const slugArray = ['percentage_split', 'conditional_split', 'upsell', 'downsell', 'thankyou'];
    if (slugArray.includes(data.slug_type)) return;
    const nodeId = nodeStore.getDataFlow.length ? nodeStore.getDataFlow.length : 0;
    const lastElement = getLastNode();
    const newNode = {
        id: `${randomId()}`,
        type: 'custom',
        position: { x: 450, y: nodeId === 0 ? 100 : (lastElement.position.y + 100) },
        data: data,
        label: `custom node`,
    }
    nodeStore.addCustomNode(newNode);
    nodeStore.updateActiveStepData(data);
    nodeStore.openModal();
    nodeStore.closeStepsSidebar();
    nodeStore.closeNodeClickedModal();
}

</script>

<template>
    <div class="node_card">
        <div class="node_card_body" @click="openSidebarFunnel(data)">

            <div v-if="data?.slug_type === 'conditional_split'" class="img_wrap">
                <div class="conditional_split custom_logic conditional_rp">
                    <Icon :name="data?.img_default" class="check_list_icon" />
                </div>
            </div>
            <div v-else-if="data?.slug_type === 'percentage_split'" class="img_wrap">
                <div class="percentage_split custom_logic">
                    <h5>A/B</h5>
                </div>
            </div>
            <div v-else class="img_wrap">
                <img :src="data?.img_default ? data?.img_default : placehoderImg" class="image" />
            </div>
            <div class="card_details card_name">
                <div class="card_info">
                    <h5>{{ data?.title ? data?.title : '' }}</h5>
                    <p>{{ data?.description ? data?.description : 'Step Description here' }}</p>
                </div>
            </div>
        </div>

    </div>
</template>

