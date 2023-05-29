import { useNodeStore } from "@/store/nodes";
const getOnlyNumber = (dataSet) => {
  const matches = dataSet?.match(/\d+/g);
  if (matches) {
    const number = parseInt(matches[0]);
    return number;
  }
};
const isObjectEmpty = (objectName) => {
  return (
    objectName &&
    Object.keys(objectName).length === 0 &&
    objectName.constructor === Object
  );
};
const randomId = () => {
  const char_set =
    "abcdefghijlkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  const length = 10;
  function max_random_number(max) {
    return Math.floor(Math.random() * max);
  }

  let random_string = "";
  for (let i = 0; i < length; i++) {
    random_string += char_set[max_random_number(char_set.length - 1)];
  }

  return random_string;
};
const checkForStepType = (node_slug, stepsArr, invert = false) => {
  const slugArray = stepsArr;
  return invert
    ? slugArray?.includes(node_slug)
      ? false
      : true
    : slugArray?.includes(node_slug)
    ? true
    : false;
};
// for not including

const getLastNode = () => {
  const nodeStore = useNodeStore();
  const nodesList = nodeStore.getDataFlow.filter(
    (node) => node.label === "custom"
  );
  return nodesList[nodesList.length - 1];
};

// get all edges of Funnel
const getAllEdges = () => {
  const nodeStore = useNodeStore();
  return nodeStore.getDataFlow?.filter((edge) => edge.type == "default");
};
const updateEdgesStatus = (arr_name, condition_value, valueToUpdate) => {
  const nodeStore = useNodeStore();
  const tempArray = [...nodeStore.getDataFlow];
  tempArray.forEach((item) => {
    const option = item.data[arr_name]?.find(
      (opt) => opt.value == condition_value
    );
    if (option) {
      option.disabled = valueToUpdate;
    }
  });
  return tempArray;
};

// get product checkout id
const getProductId = () => {
  const nodeStore = useNodeStore();
  const stepData = nodeStore.getDataFlow.find(
    (item) => item?.data?.slug_type === "checkout"
  );
  return stepData?.data?.step_product_id
    ? stepData?.data?.step_product_id
    : null;
};

export {
  getOnlyNumber,
  isObjectEmpty,
  randomId,
  getAllEdges,
  updateEdgesStatus,
  getProductId,
  checkForStepType,
  getLastNode,
};
