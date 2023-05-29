<script setup>
import Icon from "../Icons/Icon.vue";
import Switch from "@/components/inputs/Switch.vue";
import Logo from "./Logo.vue";
import angle_left from "@/assets/icons/angle_left.svg";
import vert_more from "@/assets/icons/vert_more.svg";
import BaseButton from "../buttons/BaseButton.vue";
import BaseDropdown from "../dropdowns/BaseDropdown.vue";
import { toast } from "vue3-toastify";

import { ref, onMounted, onUpdated } from "vue";
import { saveFunnel, toastOption } from "@/api/funnel/funnel-service";
import { useNodeStore } from "@/store/nodes";

let statChecked = ref(false);
let enabledChecked = ref(false);
let saveBtnEle = ref(null);
const nodeStore = useNodeStore();

onMounted(() => {
  saveBtnEle.value = saveBtnEle.value.buttonRef;
});
onUpdated(() => {
  saveBtnEle.value = saveBtnEle.value.buttonRef;
});
const saveHandler = async () => {
  if (nodeStore.getFunnelId === null) {
    toast.warning("Funnel id Missing", toastOption);
    return;
  }
  if (saveBtnEle.value) {
    saveBtnEle.value.disabled = true;
    saveBtnEle.value.setAttribute("loader-indicator", "on");
  }
  // const data = {
  //   funnel_id: nodeStore.getFunnelId,
  //   funnel_data: nodeStore.getDataFlow,
  //   funnel_enabled: enabledChecked.value,
  //   funnel_identifier: [],
  //   mintSteps: [],
  //   should_update_steps: true,
  //   should_update_steps_order: true,
  // };
  await saveFunnel(enabledChecked.value);
  saveBtnEle.value?.removeAttribute("loader-indicator");
  saveBtnEle.value.disabled = false;
};

const statsToggle = () => {
  statChecked.value = !statChecked.value;
  nodeStore.toggleStatsVisibility();
};
const enableToggle = () => {
  enabledChecked.value = !enabledChecked.value;
};
</script>

<template>
  <div class="navbar_wrapper">
    <div class="left_section">
      <div class="back_btn me-2">
        <Icon :name="angle_left" class="icon_size" />
      </div>
      <div class="logo_wrapper">
        <Logo />
      </div>
    </div>
    <div class="right_section">
      <div class="stats_switch r_item">
        <Switch key="1" @on-change="statsToggle" :isChecked="statChecked" :title="'Stats'" />
      </div>
      <div class="stats_switch r_item">
        <Switch key="2" @on-change="enableToggle" :isChecked="enabledChecked" :title="'Enabled'" />
      </div>
      <div class="btn_wrap r_item">
        <BaseButton ref="saveBtnEle" @on-click="saveHandler" :is-disabled="nodeStore.modalStatus">
          <span class="spinner-border spinner_c spinner-border-sm" role="status" aria-hidden="true"></span>
          <span class="save__">save</span>
        </BaseButton>
      </div>
      <div class="more_action r_item">
        <Icon :name="vert_more" class="icon_size" />
        <div class="dropdown_wrapper_box">
          <BaseDropdown />
        </div>
      </div>
    </div>
  </div>
</template>
<style lang="scss">
.navbar_wrapper {
  display: flex;
  align-items: center;
  width: 100%;
  justify-content: space-between;

  .back_btn {
    padding: 0 10px;
    cursor: pointer;

    .icon_size {
      width: 20px;
      height: 20px;
    }
  }

  .left_section {
    display: flex;
    align-items: center;
  }

  .right_section {
    display: flex;
    align-items: center;

    .r_item {
      margin-left: 14px;
      margin-right: 14px;

      &:first-child {
        margin-right: 0;
      }

      &:last-child {
        margin-right: 0;
      }
    }

    .edit_icon {
      padding-right: 14px;
      cursor: pointer;
    }

    .icon_size {
      width: 24px;
      height: 24px;
    }
  }
}

.more_action {
  position: relative;
  cursor: pointer;

  .dropdown_wrapper_box {
    display: none;
    position: absolute;
    top: 0;
    right: -42px;
    background: TRANSPARENT;
    z-index: 99;
    padding-top: 52px;
    width: 140px;
  }

  &:hover {
    .icon_size {
      background-color: #efefef !important;
      border-radius: 2px;
    }

    .dropdown_wrapper_box {
      display: block;
      transition: all 0.5 ease-in-out;
      transform: translate(-50px, 0);
    }
  }
}
</style>
