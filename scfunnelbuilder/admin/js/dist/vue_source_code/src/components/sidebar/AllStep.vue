<script setup>
import CardNode from '../CardNode.vue';

import angle_left from "@/assets/icons/left_angle_white.svg"
import angle_right from "@/assets/icons/right_angle_white.svg"
import Icon from '../Icons/Icon.vue';
import { useNodeStore } from '@/store/nodes';
import { nodeList } from "@/utils/initial-node";
import { reactive } from 'vue';
const nodeStore = useNodeStore();

let nodeData = reactive({
    data: {}
})

const emit = defineEmits(['getDragedNodeData']);


function onDragStart(event, nodeType, nodeData) {
    nodeData.data = nodeData;
    if (event.dataTransfer) {
        event.dataTransfer.setData('application/vueflow', nodeType);
        event.dataTransfer.effectAllowed = 'move'
    }
    emit('getDragedNodeData', nodeData.data);
}
const toggleStepper = () => {
    if (nodeStore.modalStatus) return;
    nodeStore.toggleStepsSidebar();
    nodeStore.closeNodeClickedModal();
}

</script>
<template>
    <div>
        <aside class="steps_left_sidebar" :class="[nodeStore.stepsSidebarStatus ? 'open' : '']">
            <div class="steps_container">
                <div :key="index" class="side_steps_wrapper me-2" v-for="(node, index) in nodeList">
                    <div class="title_wrapper mb-4">
                        <h5 class="title">
                            {{ node.title }}
                        </h5>
                    </div>
                    <div class="nodes custom_node_step">
                        <div class="vue-flow__node-default custom_node_type" @click="clickHandler" :key="index"
                            :draggable="true" v-for="(nodeInfo, index) in node.list"
                            @dragstart="onDragStart($event, nodeInfo.nodeType, nodeInfo.data)">
                            <CardNode :data="nodeInfo.data" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="toggle_stepper">
                <a class="toggle_sidebar" @click="toggleStepper">
                    <Icon :name="nodeStore.stepsSidebarStatus ? angle_left : angle_right" class="icon_tg" />
                </a>
            </div>
        </aside>
    </div>
</template>