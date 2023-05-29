<script setup>
import Icon from '../Icons/Icon.vue';
import edit from "@/assets/icons/pencil.svg";
import copy from "@/assets/icons/file_copy.svg";
import remove from "@/assets/icons/bin.svg";
import { useNodeStore } from '@/store/nodes';
import { checkForStepType, randomId } from '@/utils/Common';
import { ref } from 'vue';
import { deleteStepNode, saveFunnel } from '@/api/funnel/funnel-service';

const nodeStore = useNodeStore();

const actionsList = [
    {
        id: "1",
        icon: edit,
    },
    {
        id: "2",
        icon: copy,
    },
    {
        id: "3",
        icon: remove
    },
]
const getActionTypes = (id) => {
    return {
        '1': 'edit',
        '2': 'copy',
        '3': 'remove',
    }[id]
}
const props = defineProps({
    hoverNodeId: String,
    edgeLabel: String
})
const actionClickHandler = async (actionId) => {
    const { data, position } = nodeStore.getNodeDetailsById(props.hoverNodeId);
    if (getActionTypes(actionId) === 'edit') {  // on edit of a node
        nodeStore.updateActiveStepData(data);
        window.open(data?.step_edit_link, '_blank');
    } else if (getActionTypes(actionId) === 'copy') {
        const newNode = {
            id: `${randomId()}`,
            type: 'custom',
            position: { x: position.x, y: position.y + 100 },
            data: {
                title: data?.title,
                sourceVisible: data?.sourceVisible,
                targetVisible: data?.targetVisible,
                slug_type: data?.slug_type,
                description: data?.step_title ? data?.step_title : data?.step_description,
                step_title: "",
            },
            label: `custom`,
        }
        nodeStore.addCustomNode(newNode);
        nodeStore.updateActiveStepData(data);
        nodeStore.openModal();
        nodeStore.closeStepsSidebar();
        nodeStore.closeNodeClickedModal();
    } else if (getActionTypes(actionId) === 'remove') {
        const stepId = data?.step_id ? data?.step_id : props.hoverNodeId;
        const edge = nodeStore.dataFlow.filter((node) => node.target == props.hoverNodeId)
        const edgeLabel = edge[0]?.label ? edge[0]?.label : '';
        await deleteStepNode(stepId, props.hoverNodeId, edgeLabel);
        nodeStore.deleteNode(props.hoverNodeId);
        await saveFunnel();
    }
}

</script>
<template>
    <div v-if="actionsList" class="action_row">
        <div class="icon_wrap" v-for="{ icon, id, } in actionsList" :key="id">
            <a v-if="id === '1'"
                :href="nodeStore.getNodeDetailsById(hoverNodeId)?.data?.step_edit_link ? nodeStore.getNodeDetailsById(hoverNodeId)?.data?.step_edit_link : null"
                target="_blank">
                <span
                    v-if="checkForStepType(nodeStore.getNodeDetailsById(hoverNodeId)?.data?.slug_type, ['conditional_split', 'percentage_split'], true)">
                    <Icon :name="icon" @click="actionClickHandler(id)" class="icon_tt" />
                </span>
            </a>
            <span v-else>
                <Icon :name="icon" @click="actionClickHandler(id)" class="icon_tt" />
            </span>
        </div>
    </div>
</template>