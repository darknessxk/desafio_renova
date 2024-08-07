import {Project} from "@/types/Project";

export interface User {
    id: number;
    email: string;
    first_name: string;
    last_name: string;
    projects?: Project[];
}