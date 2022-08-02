import Enzyme from 'enzyme';
import Adapter from '@wojtekmaj/enzyme-adapter-react-17';
import {createSerializer} from 'enzyme-to-json';
import '@testing-library/jest-dom'
// Polyfills to avoid runtime errors
import 'core-js/stable';
import 'regenerator-runtime/runtime';

Enzyme.configure({ adapter: new Adapter() });

expect.addSnapshotSerializer(createSerializer({mode: 'deep'}) as any);