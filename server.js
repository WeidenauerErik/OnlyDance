import express from "express";
import cors from 'cors';

const app = express();
const port = 4000;

app.use(cors())

app.get('/', (req, res) => {
  res.send(steps);
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});

const steps = [
  {
    howQuick: 2,  //base
    woman: {
      leftFoot: { height: 0.5, width: 0.55, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.55, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 0.001,  //Start 1/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 2, //Wiege 2/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.3, width: 0.6, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.5, width: 0.6, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 2,  //Wiege 3/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.3, width: 0.6, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.5, width: 0.6, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 1,  //cha 4/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 1,  //cha 4,5/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 2,  //cha 1/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 2,  //Wiege 2/4
    woman: {
      leftFoot: { height: 0.7, width: 0.35, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.9, width: 0.35, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 2,  //Wiege 3/4
    woman: {
      leftFoot: { height: 0.7, width: 0.35, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.9, width: 0.35, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 1,  //cha 4/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 1,  //cha 4,5/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },

  //-----------------------------------------------------Wiederhohlung--------------------------------------------------//
  {
    howQuick: 2,  //Start 1/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 2, //Wiege 2/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.3, width: 0.6, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.5, width: 0.6, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 2,  //Wiege 3/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.3, width: 0.6, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.5, width: 0.6, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 1,  //cha 4/4
    woman: {
      leftFoot: { height: 0.5, width: 0.65, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.65, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 1,  //cha 4,5/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 2,  //cha 1/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 2,  //Wiege 2/4
    woman: {
      leftFoot: { height: 0.7, width: 0.35, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.9, width: 0.35, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 2,  //Wiege 3/4
    woman: {
      leftFoot: { height: 0.7, width: 0.35, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.9, width: 0.35, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
  {
    howQuick: 1,  //cha 4/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.5, width: 0.3, rotate: 180, footToesActive: false, footHeelActive: false, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.3, rotate: 0, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: true, footHeelActive: true, }, }, },
  {
    howQuick: 1,  //cha 4,5/4
    woman: {
      leftFoot: { height: 0.5, width: 0.50, rotate: 180, footToesActive: false, footHeelActive: false, },
      rightFoot: { height: 0.5, width: 0.45, rotate: 180, footToesActive: true, footHeelActive: true, }, },
    man: {
      leftFoot: { height: 0.7, width: 0.45, rotate: 0, footToesActive: true, footHeelActive: true, },
      rightFoot: { height: 0.7, width: 0.50, rotate: 0, footToesActive: false, footHeelActive: false, }, }, },
]